<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class User
{
    /* -------------------------------------------------- */
    /* recherche utilisateur */
    /* -------------------------------------------------- */

    /* Vérifie si un email existe déjà. */
    public static function emailExists(string $email): bool
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT id FROM users WHERE email = :email LIMIT 1'
        );

        $query->execute([
            'email' => $email,
        ]);

        return $query->fetch() !== false;
    }

    /* Recherche un utilisateur à partir de son adresse mail. */
    public static function findByEmail(string $email): ?array
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
            users.id,
            users.first_name,
            users.last_name,
            users.email,
            users.password_hash,
            users.is_active,
            roles.name AS role
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        WHERE users.email = :email
        LIMIT 1'
        );

        $query->execute([
            'email' => $email,
        ]);

        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return $user;
    }

    /* -------------------------------------------------- */
    /* recherche rôle */
    /* -------------------------------------------------- */

    /* Récupère l'identifiant du rôle utilisateur. */
    public static function getUserRoleId(): ?int
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT id FROM roles WHERE name = :name LIMIT 1'
        );

        $query->execute([
            'name' => 'user',
        ]);

        $role = $query->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            return null;
        }

        return (int) $role['id'];
    }

    /* -------------------------------------------------- */
    /* création utilisateur */
    /* -------------------------------------------------- */

    /* Crée un nouveau compte utilisateur. */
    public static function create(array $data): bool
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'INSERT INTO users (
                role_id,
                first_name,
                last_name,
                phone,
                email,
                address,
                postal_code,
                city,
                password_hash,
                is_active
            ) VALUES (
                :role_id,
                :first_name,
                :last_name,
                :phone,
                :email,
                :address,
                :postal_code,
                :city,
                :password_hash,
                TRUE
            )'
        );

        return $query->execute([
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'city' => $data['city'],
            'password_hash' => $data['password_hash'],
        ]);
        
    }
    /* -------------------------------------------------- */
/* mise à jour du mot de passe */
/* -------------------------------------------------- */

/* Enregistre le nouveau mot de passe de l'utilisateur. */
public static function updatePassword(
    int $userId,
    string $passwordHash
): bool {
    $connection = Database::getConnection();

    $query = $connection->prepare(
        'UPDATE users
        SET password_hash = :password_hash,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
            AND is_active = TRUE'
    );

    $query->execute([
        'password_hash' => $passwordHash,
        'id' => $userId,
    ]);

    return $query->rowCount() === 1;
}
}

