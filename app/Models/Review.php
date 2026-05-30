<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class Review
{
    /* -------------------------------------------------- */
    /* récupération des avis validés */
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
}
