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
    /* commandes d'un utilisateur */
    /* -------------------------------------------------- */

    /* Récupère les commandes d'un utilisateur connecté. */
    public static function findAllByUserId(int $userId): array
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                orders.id,
                orders.order_number,
                orders.event_date,
                orders.delivery_time,
                orders.people_count,
                orders.total_price,
                orders.created_at,
                menus.title AS menu_title,
                order_statuses.name AS status_name
            FROM orders
            INNER JOIN menus
                ON menus.id = orders.menu_id
            INNER JOIN order_statuses
                ON order_statuses.id = orders.current_status_id
            WHERE orders.user_id = :user_id
            ORDER BY orders.created_at DESC'
        );

        $query->execute([
            'user_id' => $userId,
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Récupère le détail d'une commande appartenant au client. */
    public static function findByIdAndUserId(
        int $orderId,
        int $userId
    ): ?array {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                orders.id,
                orders.order_number,
                orders.user_id,
                orders.menu_id,
                orders.customer_first_name,
                orders.customer_last_name,
                orders.customer_email,
                orders.customer_phone,
                orders.delivery_address,
                orders.delivery_postal_code,
                orders.delivery_city,
                orders.event_date,
                orders.delivery_time,
                orders.people_count,
                orders.menu_price,
                orders.delivery_price,
                orders.total_price,
                orders.created_at,
                orders.updated_at,
                menus.title AS menu_title,
                menus.description AS menu_description,
                order_statuses.name AS status_name
            FROM orders
            INNER JOIN menus
                ON menus.id = orders.menu_id
            INNER JOIN order_statuses
                ON order_statuses.id = orders.current_status_id
            WHERE orders.id = :order_id
                AND orders.user_id = :user_id
            LIMIT 1'
        );

        $query->execute([
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);

        $order = $query->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    /* Récupère l'historique d'une commande appartenant au client. */
    public static function findStatusHistoryByOrderAndUser(
        int $orderId,
        int $userId
    ): array {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                order_status_history.id,
                order_status_history.note,
                order_status_history.created_at,
                order_statuses.name AS status_name
            FROM order_status_history
            INNER JOIN orders
                ON orders.id = order_status_history.order_id
            INNER JOIN order_statuses
                ON order_statuses.id =
                    order_status_history.status_id
            WHERE order_status_history.order_id = :order_id
                AND orders.user_id = :user_id
            ORDER BY
                order_status_history.created_at ASC,
                order_status_history.id ASC'
        );

        $query->execute([
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

        /* -------------------------------------------------- */
    /* modification d'une commande */
    /* -------------------------------------------------- */

    /*
     * Modifie une commande en attente et ajuste son stock.
     * Le menu enregistré dans la commande reste inchangé.
     */
    public static function updateByUser(
        int $orderId,
        int $userId,
        array $data
    ): string {
        $connection = Database::getConnection();

        try {
            $connection->beginTransaction();

            /*
             * Verrouille la commande et revérifie son propriétaire
             * ainsi que son statut.
             */
            $orderQuery = $connection->prepare(
                'SELECT
                    orders.id,
                    orders.menu_id,
                    orders.people_count,
                    order_statuses.name AS status_name
                FROM orders
                INNER JOIN order_statuses
                    ON order_statuses.id =
                        orders.current_status_id
                WHERE orders.id = :order_id
                    AND orders.user_id = :user_id
                LIMIT 1
                FOR UPDATE'
            );

            $orderQuery->execute([
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);

            $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                $connection->rollBack();

                return 'not_found';
            }

            if ($order['status_name'] !== 'En attente') {
                $connection->rollBack();

                return 'not_allowed';
            }

            $menuId = (int) $order['menu_id'];
            $oldPeopleCount = (int) $order['people_count'];
            $newPeopleCount = (int) $data['people_count'];

            /* Verrouille également le menu initial. */
            $menuQuery = $connection->prepare(
                'SELECT id, stock_quantity
                FROM menus
                WHERE id = :menu_id
                    AND is_active = TRUE
                LIMIT 1
                FOR UPDATE'
            );

            $menuQuery->execute([
                'menu_id' => $menuId,
            ]);

            $menu = $menuQuery->fetch(PDO::FETCH_ASSOC);

            if (!$menu) {
                $connection->rollBack();

                return 'not_found';
            }

            /*
             * Le stock actuel ne contient plus les portions déjà
             * réservées par cette commande.
             */
            $peopleDifference =
                $newPeopleCount - $oldPeopleCount;

            if ($peopleDifference > 0) {
                $stockQuery = $connection->prepare(
                    'UPDATE menus
                    SET stock_quantity =
                        stock_quantity - :difference
                    WHERE id = :menu_id
                        AND stock_quantity >= :difference'
                );

                $stockQuery->execute([
                    'difference' => $peopleDifference,
                    'menu_id' => $menuId,
                ]);

                if ($stockQuery->rowCount() !== 1) {
                    $connection->rollBack();

                    return 'insufficient_stock';
                }
            }

            if ($peopleDifference < 0) {
                $stockQuery = $connection->prepare(
                    'UPDATE menus
                    SET stock_quantity =
                        stock_quantity + :difference
                    WHERE id = :menu_id'
                );

                $stockQuery->execute([
                    'difference' => abs($peopleDifference),
                    'menu_id' => $menuId,
                ]);

                if ($stockQuery->rowCount() !== 1) {
                    $connection->rollBack();

                    return 'error';
                }
            }

            /*
             * Le menu_id n'est volontairement jamais modifié.
             */
            $updateQuery = $connection->prepare(
                'UPDATE orders
                SET customer_first_name =
                        :customer_first_name,
                    customer_last_name =
                        :customer_last_name,
                    customer_email =
                        :customer_email,
                    customer_phone =
                        :customer_phone,
                    delivery_address =
                        :delivery_address,
                    delivery_postal_code =
                        :delivery_postal_code,
                    delivery_city =
                        :delivery_city,
                    event_date =
                        :event_date,
                    delivery_time =
                        :delivery_time,
                    people_count =
                        :people_count,
                    menu_price =
                        :menu_price,
                    delivery_price =
                        :delivery_price,
                    total_price =
                        :total_price,
                    updated_at =
                        CURRENT_TIMESTAMP
                WHERE id = :order_id
                    AND user_id = :user_id
                    AND current_status_id = (
                        SELECT id
                        FROM order_statuses
                        WHERE name = :status_name
                        LIMIT 1
                    )'
            );

            $updateQuery->execute([
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
                'event_date' =>
                    $data['event_date'],
                'delivery_time' =>
                    $data['delivery_time'],
                'people_count' =>
                    $newPeopleCount,
                'menu_price' =>
                    $data['menu_price'],
                'delivery_price' =>
                    $data['delivery_price'],
                'total_price' =>
                    $data['total_price'],
                'order_id' =>
                    $orderId,
                'user_id' =>
                    $userId,
                'status_name' =>
                    'En attente',
            ]);

            if ($updateQuery->rowCount() !== 1) {
                $connection->rollBack();

                return 'not_allowed';
            }

            $connection->commit();

            return 'updated';
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            return 'error';
        }
    }

    /* -------------------------------------------------- */
    /* annulation d'une commande */
    /* -------------------------------------------------- */

    /*
     * Annule une commande en attente et restitue son stock.
     * Retourne le résultat du traitement au contrôleur.
     */
    public static function cancelByUser(
        int $orderId,
        int $userId
    ): string {
        $connection = Database::getConnection();

        try {
            $connection->beginTransaction();

            /*
             * Verrouille la commande pendant toute l'annulation.
             * Le propriétaire et le statut sont revérifiés en base.
             */
            $orderQuery = $connection->prepare(
                'SELECT
                    orders.id,
                    orders.menu_id,
                    orders.people_count,
                    order_statuses.name AS status_name
                FROM orders
                INNER JOIN order_statuses
                    ON order_statuses.id =
                        orders.current_status_id
                WHERE orders.id = :order_id
                    AND orders.user_id = :user_id
                LIMIT 1
                FOR UPDATE'
            );

            $orderQuery->execute([
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);

            $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                $connection->rollBack();

                return 'not_found';
            }

            /* L'annulation est possible uniquement avant acceptation. */
            if ($order['status_name'] !== 'En attente') {
                $connection->rollBack();

                return 'not_allowed';
            }

            /* Récupère l'identifiant du statut annulé. */
            $statusQuery = $connection->prepare(
                'SELECT id
                FROM order_statuses
                WHERE name = :name
                LIMIT 1'
            );

            $statusQuery->execute([
                'name' => 'Annulée',
            ]);

            $cancelledStatus = $statusQuery->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$cancelledStatus) {
                $connection->rollBack();

                return 'error';
            }

            $cancelledStatusId =
                (int) $cancelledStatus['id'];

            /*
             * Modifie le statut seulement si la commande est toujours
             * dans son état initial.
             */
            $updateQuery = $connection->prepare(
                'UPDATE orders
                SET current_status_id = :status_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :order_id
                    AND user_id = :user_id
                    AND current_status_id = (
                        SELECT id
                        FROM order_statuses
                        WHERE name = :current_status
                        LIMIT 1
                    )'
            );

            $updateQuery->execute([
                'status_id' => $cancelledStatusId,
                'order_id' => $orderId,
                'user_id' => $userId,
                'current_status' => 'En attente',
            ]);

            if ($updateQuery->rowCount() !== 1) {
                $connection->rollBack();

                return 'not_allowed';
            }

            /* Restitue les quantités réservées au stock du menu. */
            $stockQuery = $connection->prepare(
                'UPDATE menus
                SET stock_quantity =
                    stock_quantity + :people_count
                WHERE id = :menu_id'
            );

            $stockQuery->execute([
                'people_count' =>
                (int) $order['people_count'],
                'menu_id' => (int) $order['menu_id'],
            ]);

            if ($stockQuery->rowCount() !== 1) {
                $connection->rollBack();

                return 'error';
            }

            /* Ajoute l'annulation dans le suivi de la commande. */
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
                'status_id' => $cancelledStatusId,
                'changed_by_user_id' => $userId,
                'note' => 'Commande annulée par le client.',
            ]);

            $connection->commit();

            return 'cancelled';
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            return 'error';
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
