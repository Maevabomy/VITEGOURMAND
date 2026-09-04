<?php

namespace App\Models;

use App\Services\Database;
use PDO;
use Throwable;

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
                id,
                day_number,
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

    /* -------------------------------------------------- */
    /* modification des horaires */
    /* -------------------------------------------------- */

    /* Met à jour tous les horaires de la semaine. */
    public static function updateWeek(array $openingHours): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $sql = '
                UPDATE opening_hours
                SET
                    opening_time = :opening_time,
                    closing_time = :closing_time,
                    is_closed = :is_closed
                WHERE day_number = :day_number
            ';

            $statement = $pdo->prepare($sql);

            foreach ($openingHours as $openingHour) {
                $statement->execute([
                    'opening_time' =>
                    $openingHour['opening_time'],
                    'closing_time' =>
                    $openingHour['closing_time'],
                    'is_closed' =>
                    $openingHour['is_closed'],
                    'day_number' =>
                    $openingHour['day_number'],
                ]);
            }

            $pdo->commit();

            return true;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return false;
        }
    }
}
