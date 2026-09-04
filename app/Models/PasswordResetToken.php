<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class PasswordResetToken
{
    /* -------------------------------------------------- */
    /* création du jeton */
    /* -------------------------------------------------- */

    /* Supprime les anciens jetons puis enregistre le nouveau. */
    public static function create(
        int $userId,
        string $tokenHash,
        string $expiresAt
    ): bool {
        $connection = Database::getConnection();

        $connection->beginTransaction();

        try {
            $deleteQuery = $connection->prepare(
                'DELETE FROM password_reset_tokens
                WHERE user_id = :user_id'
            );

            $deleteQuery->execute([
                'user_id' => $userId,
            ]);

            $insertQuery = $connection->prepare(
                'INSERT INTO password_reset_tokens (
                    user_id,
                    token,
                    expires_at
                ) VALUES (
                    :user_id,
                    :token,
                    :expires_at
                )'
            );

            $created = $insertQuery->execute([
                'user_id' => $userId,
                'token' => $tokenHash,
                'expires_at' => $expiresAt,
            ]);

            $connection->commit();

            return $created;
        } catch (\Throwable $exception) {
            $connection->rollBack();

            return false;
        }
    }

    /* -------------------------------------------------- */
    /* recherche du jeton */
    /* -------------------------------------------------- */

    /* Recherche un jeton valide et non utilisé. */
    public static function findValid(string $tokenHash): ?array
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                password_reset_tokens.id,
                password_reset_tokens.user_id,
                password_reset_tokens.expires_at,
                users.email
            FROM password_reset_tokens
            INNER JOIN users
                ON users.id = password_reset_tokens.user_id
            WHERE password_reset_tokens.token = :token
                AND password_reset_tokens.used_at IS NULL
                AND password_reset_tokens.expires_at > NOW()
                AND users.is_active = TRUE
            LIMIT 1'
        );

        $query->execute([
            'token' => $tokenHash,
        ]);

        $resetToken = $query->fetch(PDO::FETCH_ASSOC);

        if (!$resetToken) {
            return null;
        }

        return $resetToken;
    }

    /* -------------------------------------------------- */
    /* utilisation du jeton */
    /* -------------------------------------------------- */

    /* Marque le jeton comme utilisé. */
    public static function markAsUsed(int $tokenId): bool
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'UPDATE password_reset_tokens
            SET used_at = NOW()
            WHERE id = :id
                AND used_at IS NULL'
        );

        return $query->execute([
            'id' => $tokenId,
        ]);
    }
}
