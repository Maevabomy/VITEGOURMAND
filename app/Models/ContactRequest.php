<?php

namespace App\Models;

use App\Services\Database;
use PDOException;

class ContactRequest
{
    /*
    |--------------------------------------------------------------------------
    | création d'une demande
    |--------------------------------------------------------------------------
    */

    /* Enregistre un message envoyé depuis la page de contact. */
    public static function create(
        string $email,
        string $subject,
        string $message
    ): bool {
        $connection = Database::getConnection();

        try {
            $query = $connection->prepare(
                '
                    INSERT INTO contact_requests (
                        email,
                        subject,
                        message
                    ) VALUES (
                        :email,
                        :subject,
                        :message
                    )
                '
            );

            return $query->execute([
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            ]);
        } catch (PDOException $exception) {
            return false;
        }
    }
}
