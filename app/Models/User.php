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

    /* Vérifie si un email appartient déjà à un autre compte. */
    public static function emailExistsForAnotherUser(
        string $email,
        int $userId
    ): bool {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT id
            FROM users
            WHERE email = :email
                AND id != :user_id
            LIMIT 1'
        );

        $query->execute([
            'email' => $email,
            'user_id' => $userId,
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

    /* Recherche un utilisateur actif à partir de son identifiant. */
    public static function findById(int $id): ?array
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
            users.id,
            users.first_name,
            users.last_name,
            users.phone,
            users.email,
            users.address,
            users.postal_code,
            users.city,
            users.is_active,
            roles.name AS role
        FROM users
        INNER JOIN roles
            ON roles.id = users.role_id
        WHERE users.id = :id
            AND users.is_active = TRUE
        LIMIT 1'
        );

        $query->execute([
            'id' => $id,
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
    /* gestion des employés */
    /* -------------------------------------------------- */

    /* Récupère l'identifiant du rôle employé. */
    public static function getEmployeeRoleId(): ?int
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT id
            FROM roles
            WHERE name = :name
            LIMIT 1'
        );

        $query->execute([
            'name' => 'employee',
        ]);

        $role = $query->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            return null;
        }

        return (int) $role['id'];
    }

    /* Récupère tous les comptes employés. */
    public static function getAllEmployees(): array
    {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                users.id,
                users.email,
                users.is_active,
                users.created_at
            FROM users
            INNER JOIN roles
                ON roles.id = users.role_id
            WHERE roles.name = :role_name
            ORDER BY
                users.is_active DESC,
                users.email ASC'
        );

        $query->execute([
            'role_name' => 'employee',
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Recherche uniquement un compte employé. */
    public static function findEmployeeById(
        int $employeeId
    ): ?array {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'SELECT
                users.id,
                users.email,
                users.is_active,
                users.created_at
            FROM users
            INNER JOIN roles
                ON roles.id = users.role_id
            WHERE users.id = :employee_id
                AND roles.name = :role_name
            LIMIT 1'
        );

        $query->execute([
            'employee_id' => $employeeId,
            'role_name' => 'employee',
        ]);

        $employee = $query->fetch(PDO::FETCH_ASSOC);

        return $employee ?: null;
    }

    /* Crée uniquement un compte employé. */
    public static function createEmployee(
        string $email,
        string $passwordHash
    ): bool {
        $employeeRoleId =
            self::getEmployeeRoleId();

        if ($employeeRoleId === null) {
            return false;
        }

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
            'role_id' => $employeeRoleId,
            'first_name' => 'Employé',
            'last_name' => '',
            'phone' => '',
            'email' => $email,
            'address' => '',
            'postal_code' => '',
            'city' => '',
            'password_hash' => $passwordHash,
        ]);
    }

    /* Active ou désactive uniquement un compte employé. */
    public static function setEmployeeActive(
        int $employeeId,
        bool $isActive
    ): bool {
        $employeeRoleId =
            self::getEmployeeRoleId();

        if ($employeeRoleId === null) {
            return false;
        }

        $connection = Database::getConnection();

        $query = $connection->prepare(
            'UPDATE users
            SET
                is_active = :is_active,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :employee_id
                AND role_id = :employee_role_id'
        );

        return $query->execute([
            'is_active' => $isActive ? 1 : 0,
            'employee_id' => $employeeId,
            'employee_role_id' => $employeeRoleId,
        ]);
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
    /* mise à jour du profil */
    /* -------------------------------------------------- */

    /* Modifie les informations personnelles du compte. */
    public static function updateProfile(
        int $userId,
        array $data
    ): bool {
        $connection = Database::getConnection();

        $query = $connection->prepare(
            'UPDATE users
            SET first_name = :first_name,
                last_name = :last_name,
                phone = :phone,
                email = :email,
                address = :address,
                postal_code = :postal_code,
                city = :city,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :user_id
                AND is_active = TRUE'
        );

        return $query->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'city' => $data['city'],
            'user_id' => $userId,
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
