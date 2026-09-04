<?php

namespace App\Models;

use App\Services\Database;
use PDO;
use Throwable;

class Review
{
    /* -------------------------------------------------- */
    /* récupération des avis */
    /* -------------------------------------------------- */

    /* Récupère les avis validés pour la page d'accueil. */
    public static function getApprovedReviews(): array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                reviews.rating,
                reviews.comment,
                users.first_name,
                users.last_name
            FROM reviews
            INNER JOIN users
                ON users.id = reviews.user_id
            WHERE reviews.moderation_status = :moderation_status
            ORDER BY reviews.created_at DESC
        ';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            'moderation_status' => 'approved',
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Récupère les avis à modérer pour l'espace employé. */
    public static function findPendingForEmployee(): array
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                reviews.id,
                reviews.rating,
                reviews.comment,
                reviews.created_at,
                users.first_name,
                users.last_name,
                users.email,
                orders.order_number,
                menus.title AS menu_title
            FROM reviews
            INNER JOIN users
                ON users.id = reviews.user_id
            INNER JOIN orders
                ON orders.id = reviews.order_id
            INNER JOIN menus
                ON menus.id = orders.menu_id
            WHERE reviews.moderation_status = :status
            ORDER BY reviews.created_at ASC'
        );

        $query->execute([
            'status' => 'pending',
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Modère un avis encore en attente. Retourne false si l'avis a déjà été traité. */
    public static function moderateByEmployee(
        int $reviewId,
        string $moderationStatus
    ): bool {
        $allowedStatuses = [
            'approved',
            'refused',
        ];

        if (
            !in_array(
                $moderationStatus,
                $allowedStatuses,
                true
            )
        ) {
            return false;
        }

        $connection = Database::getConnection();

        $query = $connection->prepare(
            'UPDATE reviews
            SET moderation_status = :moderation_status
            WHERE id = :review_id
                AND moderation_status = :pending_status'
        );

        $query->execute([
            'moderation_status' => $moderationStatus,
            'review_id' => $reviewId,
            'pending_status' => 'pending',
        ]);

        return $query->rowCount() === 1;
    }

    /* Récupère l'avis déjà associé à une commande. */
    public static function findByOrderAndUser(
        int $orderId,
        int $userId
    ): ?array {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                reviews.id,
                reviews.order_id,
                reviews.user_id,
                reviews.rating,
                reviews.comment,
                reviews.moderation_status,
                reviews.created_at
            FROM reviews
            WHERE reviews.order_id = :order_id
                AND reviews.user_id = :user_id
            LIMIT 1'
        );

        $query->execute([
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);

        $review = $query->fetch(PDO::FETCH_ASSOC);

        return $review ?: null;
    }

    /* -------------------------------------------------- */
    /* création d'un avis */
    /* -------------------------------------------------- */

    /* Enregistre un avis uniquement pour une commande terminée appartenant à l'utilisateur. */
    public static function createForOrder(
        int $orderId,
        int $userId,
        int $rating,
        string $comment
    ): string {
        $connection = Database::getConnection();

        try {
            $connection->beginTransaction();

            /* Verrouille et contrôle la commande. */
            $orderQuery = $connection->prepare(
                'SELECT
                    orders.id,
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

            if ($order['status_name'] !== 'Terminée') {
                $connection->rollBack();

                return 'not_allowed';
            }

            /* Vérifie qu'aucun avis n'existe déjà. */
            $reviewQuery = $connection->prepare(
                'SELECT id
                FROM reviews
                WHERE order_id = :order_id
                LIMIT 1
                FOR UPDATE'
            );

            $reviewQuery->execute([
                'order_id' => $orderId,
            ]);

            if ($reviewQuery->fetch(PDO::FETCH_ASSOC)) {
                $connection->rollBack();

                return 'already_exists';
            }

            /* Enregistre l'avis en attente de validation. */
            $insertQuery = $connection->prepare(
                'INSERT INTO reviews (
                    order_id,
                    user_id,
                    rating,
                    comment,
                    moderation_status
                ) VALUES (
                    :order_id,
                    :user_id,
                    :rating,
                    :comment,
                    :moderation_status
                )'
            );

            $insertQuery->execute([
                'order_id' => $orderId,
                'user_id' => $userId,
                'rating' => $rating,
                'comment' => $comment,
                'moderation_status' => 'pending',
            ]);

            $connection->commit();

            return 'created';
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            return 'error';
        }
    }
}
