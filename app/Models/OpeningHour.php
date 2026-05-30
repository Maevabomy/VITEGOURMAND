<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class OpeningHour
{
    /* -------------------------------------------------- */
    /* récupération des horaires */
    /* -------------------------------------------------- */

    /* Récupère tous les horaires dans l'ordre de la semaine. */
    public static function getAll(): array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                day_name,
                opening_time,
                closing_time,
                is_closed
            FROM opening_hours
            ORDER BY day_number
        ';

        $statement = $pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}