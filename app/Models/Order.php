<?php

namespace App\Models;

use App\Services\Database;
use PDO;
use Throwable;

class Order
{
    /* -------------------------------------------------- */
    /* création d'une commande */
    /* -------------------------------------------------- */

    /* Crée la commande, son historique et diminue le stock. */
    public static function create(array $data): ?array
    {
        $connection = Database::getConnection();

        try {
            $connection->beginTransaction();

            /* Verrouille le menu pendant la transaction. */
            $menuQuery = $connection->prepare(
                'SELECT stock_quantity
                FROM menus
                WHERE id = :menu_id
                    AND is_active = TRUE
                FOR UPDATE'
            );

            $menuQuery->execute([
                'menu_id' => $data['menu_id'],
            ]);

            $menu = $menuQuery->fetch(PDO::FETCH_ASSOC);

            $peopleCount = (int) $data['people_count'];

            if (
                !$menu
                || $peopleCount < 1
                || (int) $menu['stock_quantity'] < $peopleCount
            ) {
                $connection->rollBack();

                return null;
            }

            /* Récupère le statut initial. */
            $statusQuery = $connection->prepare(
                'SELECT id
                FROM order_statuses
                WHERE name = :name
                LIMIT 1'
            );

            $statusQuery->execute([
                'name' => 'En attente',
            ]);

            $status = $statusQuery->fetch(PDO::FETCH_ASSOC);

            if (!$status) {
                $connection->rollBack();

                return null;
            }

            $statusId = (int) $status['id'];
            $orderNumber = self::generateOrderNumber();

            /* Insère la commande. */
            $orderQuery = $connection->prepare(
                'INSERT INTO orders (
                    order_number,
                    user_id,
                    menu_id,
                    current_status_id,
                    customer_first_name,
                    customer_last_name,
                    customer_email,
                    customer_phone,
                    delivery_address,
                    delivery_postal_code,
                    delivery_city,
                    event_date,
                    delivery_time,
                    people_count,
                    menu_price,
                    delivery_price,
                    total_price
                ) VALUES (
                    :order_number,
                    :user_id,
                    :menu_id,
                    :current_status_id,
                    :customer_first_name,
                    :customer_last_name,
                    :customer_email,
                    :customer_phone,
                    :delivery_address,
                    :delivery_postal_code,
                    :delivery_city,
                    :event_date,
                    :delivery_time,
                    :people_count,
                    :menu_price,
                    :delivery_price,
                    :total_price
                )'
            );

            $orderQuery->execute([
                'order_number' => $orderNumber,
                'user_id' => $data['user_id'],
                'menu_id' => $data['menu_id'],
                'current_status_id' => $statusId,
                'customer_first_name' =>
                $data['customer_first_name'],
                'customer_last_name' =>
                $data['customer_last_name'],
                'customer_email' =>
                $data['customer_email'],
                'customer_phone' =>
                $data['customer_phone'],
                'delivery_address' =>
                $data['delivery_address'],
                'delivery_postal_code' =>
                $data['delivery_postal_code'],
                'delivery_city' =>
                $data['delivery_city'],
                'event_date' => $data['event_date'],
                'delivery_time' => $data['delivery_time'],
                'people_count' => $data['people_count'],
                'menu_price' => $data['menu_price'],
                'delivery_price' => $data['delivery_price'],
                'total_price' => $data['total_price'],
            ]);

            $orderId = (int) $connection->lastInsertId();

            /* Ajoute le premier statut dans l'historique. */
            $historyQuery = $connection->prepare(
                'INSERT INTO order_status_history (
                    order_id,
                    status_id,
                    changed_by_user_id,
                    note
                ) VALUES (
                    :order_id,
                    :status_id,
                    :changed_by_user_id,
                    :note
                )'
            );

            $historyQuery->execute([
                'order_id' => $orderId,
                'status_id' => $statusId,
                'changed_by_user_id' => $data['user_id'],
                'note' => 'Commande créée par le client.',
            ]);

            /* Diminue le stock selon le nombre de personnes. */
            $stockQuery = $connection->prepare(
                'UPDATE menus
    SET stock_quantity = stock_quantity - :people_count
    WHERE id = :menu_id
        AND stock_quantity >= :people_count'
            );

            $stockQuery->execute([
                'menu_id' => $data['menu_id'],
                'people_count' => $peopleCount,
            ]);

            if ($stockQuery->rowCount() !== 1) {
                $connection->rollBack();

                return null;
            }

            $connection->commit();

            return [
                'id' => $orderId,
                'order_number' => $orderNumber,
                'status_name' => 'En attente',
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            return null;
        }
    }

    /* -------------------------------------------------- */
    /* numéro de commande */
    /* -------------------------------------------------- */

    /* Génère un numéro de commande unique. */
    private static function generateOrderNumber(): string
    {
        return 'VG-'
            . date('Ymd')
            . '-'
            . strtoupper(bin2hex(random_bytes(3)));
    }
}
