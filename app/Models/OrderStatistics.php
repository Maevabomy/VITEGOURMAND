<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class OrderStatistics
{
    /*
    |--------------------------------------------------------------------------
    | nombre de commandes par menu
    |--------------------------------------------------------------------------
    */

    /* Prépare les statistiques qui seront synchronisées vers la base MongoDB. */
    public static function getMenuOrderCounts(): array
    {
        $connection = Database::getConnection();

        $query = $connection->query(
            '
                SELECT
                    menus.id AS menu_id,
                    menus.title AS menu_title,
                    COUNT(orders.id) AS order_count
                FROM menus
                LEFT JOIN orders
                    ON orders.menu_id = menus.id
                GROUP BY
                    menus.id,
                    menus.title
                ORDER BY
                    menus.title ASC
            '
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | chiffre d'affaires
    |--------------------------------------------------------------------------
    */

    /* Récupère les menus proposés dans le filtre. */
    public static function getMenusForFilter(): array
    {
        $connection = Database::getConnection();

        $query = $connection->query(
            '
                SELECT
                    id,
                    title
                FROM menus
                ORDER BY title ASC
            '
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Calcule le chiffre d'affaires par menu. Les commandes annulées sont exclues. La période est basée sur la date de prestation. */
    public static function getRevenueByMenu(
        ?int $menuId,
        ?string $startDate,
        ?string $endDate
    ): array {
        $connection = Database::getConnection();

        $sql = '
            SELECT
                menus.id AS menu_id,
                menus.title AS menu_title,
                COUNT(orders.id) AS order_count,
                COALESCE(
                    SUM(orders.total_price),
                    0
                ) AS revenue
            FROM menus

            LEFT JOIN orders
                ON orders.menu_id = menus.id

                AND orders.current_status_id <> (
                    SELECT id
                    FROM order_statuses
                    WHERE name = :cancelled_status
                    LIMIT 1
                )
        ';

        $parameters = [
            'cancelled_status' => 'Annulée',
        ];

        if ($startDate !== null) {
            $sql .= '
                AND orders.event_date >= :start_date
            ';

            $parameters['start_date'] =
                $startDate;
        }

        if ($endDate !== null) {
            $sql .= '
                AND orders.event_date <= :end_date
            ';

            $parameters['end_date'] =
                $endDate;
        }

        $sql .= '
            WHERE 1 = 1
        ';

        if ($menuId !== null) {
            $sql .= '
                AND menus.id = :menu_id
            ';

            $parameters['menu_id'] =
                $menuId;
        }

        $sql .= '
            GROUP BY
                menus.id,
                menus.title

            ORDER BY
                revenue DESC,
                menus.title ASC
        ';

        $query = $connection->prepare($sql);

        $query->execute($parameters);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
