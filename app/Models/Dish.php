<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class Dish
{
    /* -------------------------------------------------- */
    /* récupération de la photo d'un plat */
    /* -------------------------------------------------- */

    /* Récupère la photo d'un plat grâce à son identifiant. */
    public static function findImageById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                photo,
                photo_mime_type
            FROM dishes
            WHERE id = :id
            AND photo IS NOT NULL
        ';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        $dishImage = $statement->fetch(PDO::FETCH_ASSOC);

        return $dishImage ?: null;
    }
}