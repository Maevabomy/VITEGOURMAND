<?php

namespace App\Models;

use App\Services\Database;
use PDO;
use Throwable;

class Dish
{
    /* -------------------------------------------------- */
    /* récupération des plats */
    /* -------------------------------------------------- */

    /* Récupère tous les plats pour l'espace employé. */
    public static function getAllForEmployee(): array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                dishes.id,
                dishes.name,
                dishes.description,
                dishes.dish_type,
                dishes.photo_mime_type,
                dishes.is_active,

                (
                    SELECT GROUP_CONCAT(
                        allergens.name
                        ORDER BY allergens.name
                        SEPARATOR ", "
                    )
                    FROM dish_allergen
                    INNER JOIN allergens
                        ON allergens.id =
                            dish_allergen.allergen_id
                    WHERE dish_allergen.dish_id = dishes.id
                ) AS allergen_names,

                (
                    SELECT COUNT(*)
                    FROM menu_dish
                    INNER JOIN menus
                        ON menus.id = menu_dish.menu_id
                    WHERE menu_dish.dish_id = dishes.id
                    AND menus.is_active = TRUE
                ) AS active_menu_count

            FROM dishes
            ORDER BY
                dishes.is_active DESC,
                FIELD(
                    dishes.dish_type,
                    "starter",
                    "main_course",
                    "dessert"
                ),
                dishes.name
        ';

        $statement = $pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Récupère un plat avec ses allergènes. */
    public static function findForEmployee(int $id): ?array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                id,
                name,
                description,
                dish_type,
                photo_mime_type,
                is_active
            FROM dishes
            WHERE id = :id
        ';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        $dish = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$dish) {
            return null;
        }

        $allergenSql = '
            SELECT allergen_id
            FROM dish_allergen
            WHERE dish_id = :dish_id
            ORDER BY allergen_id
        ';

        $allergenStatement = $pdo->prepare($allergenSql);

        $allergenStatement->execute([
            'dish_id' => $id,
        ]);

        $dish['allergen_ids'] = array_map(
            'intval',
            $allergenStatement->fetchAll(
                PDO::FETCH_COLUMN
            )
        );

        return $dish;
    }

    /* Récupère tous les allergènes. */
    public static function getAllAllergens(): array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                id,
                name
            FROM allergens
            ORDER BY name
        ';

        $statement = $pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Vérifie qu'un nom de plat est disponible. */
    public static function nameExists(
        string $name,
        ?int $excludedId = null
    ): bool {
        $pdo = Database::getConnection();

        $sql = '
            SELECT COUNT(*)
            FROM dishes
            WHERE name = :name
        ';

        $parameters = [
            'name' => $name,
        ];

        if ($excludedId !== null) {
            $sql .= ' AND id != :excluded_id';

            $parameters['excluded_id'] = $excludedId;
        }

        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn() > 0;
    }

    /* -------------------------------------------------- */
    /* création et modification */
    /* -------------------------------------------------- */

    /* Crée un plat et ses relations allergènes. */
    public static function create(
        array $data,
        array $allergenIds
    ): bool {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $sql = '
                INSERT INTO dishes (
                    name,
                    description,
                    dish_type,
                    photo,
                    photo_mime_type,
                    is_active
                ) VALUES (
                    :name,
                    :description,
                    :dish_type,
                    :photo,
                    :photo_mime_type,
                    TRUE
                )
            ';

            $statement = $pdo->prepare($sql);

            $statement->bindValue(
                ':name',
                $data['name']
            );

            $statement->bindValue(
                ':description',
                $data['description']
            );

            $statement->bindValue(
                ':dish_type',
                $data['dish_type']
            );

            if ($data['photo'] !== null) {
                $statement->bindValue(
                    ':photo',
                    $data['photo'],
                    PDO::PARAM_LOB
                );

                $statement->bindValue(
                    ':photo_mime_type',
                    $data['photo_mime_type']
                );
            } else {
                $statement->bindValue(
                    ':photo',
                    null,
                    PDO::PARAM_NULL
                );

                $statement->bindValue(
                    ':photo_mime_type',
                    null,
                    PDO::PARAM_NULL
                );
            }

            $statement->execute();

            $dishId = (int) $pdo->lastInsertId();

            self::replaceAllergens(
                $pdo,
                $dishId,
                $allergenIds
            );

            $pdo->commit();

            return true;
        } catch (Throwable $exception) {
            try {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            } catch (Throwable $rollbackException) {
                // La connexion peut déjà être interrompue.
            }

            return false;
        }
    }

    /* Modifie un plat et ses allergènes. */
    public static function update(
        int $id,
        array $data,
        array $allergenIds
    ): bool {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            if ($data['photo'] !== null) {
                $sql = '
                    UPDATE dishes
                    SET
                        name = :name,
                        description = :description,
                        dish_type = :dish_type,
                        photo = :photo,
                        photo_mime_type = :photo_mime_type
                    WHERE id = :id
                ';
            } else {
                $sql = '
                    UPDATE dishes
                    SET
                        name = :name,
                        description = :description,
                        dish_type = :dish_type
                    WHERE id = :id
                ';
            }

            $statement = $pdo->prepare($sql);

            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':name', $data['name']);
            $statement->bindValue(
                ':description',
                $data['description']
            );
            $statement->bindValue(
                ':dish_type',
                $data['dish_type']
            );

            if ($data['photo'] !== null) {
                $statement->bindValue(
                    ':photo',
                    $data['photo'],
                    PDO::PARAM_LOB
                );

                $statement->bindValue(
                    ':photo_mime_type',
                    $data['photo_mime_type']
                );
            }

            $statement->execute();

            self::replaceAllergens(
                $pdo,
                $id,
                $allergenIds
            );

            $pdo->commit();

            return true;
        } catch (Throwable $exception) {
            try {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            } catch (Throwable $rollbackException) {
                // La connexion peut déjà être interrompue.
            }

            return false;
        }
    }

    /* Remplace les allergènes associés à un plat. */
    private static function replaceAllergens(
        PDO $pdo,
        int $dishId,
        array $allergenIds
    ): void {
        $deleteStatement = $pdo->prepare(
            '
                DELETE FROM dish_allergen
                WHERE dish_id = :dish_id
            '
        );

        $deleteStatement->execute([
            'dish_id' => $dishId,
        ]);

        if (empty($allergenIds)) {
            return;
        }

        $insertStatement = $pdo->prepare(
            '
                INSERT INTO dish_allergen (
                    dish_id,
                    allergen_id
                ) VALUES (
                    :dish_id,
                    :allergen_id
                )
            '
        );

        foreach ($allergenIds as $allergenId) {
            $insertStatement->execute([
                'dish_id' => $dishId,
                'allergen_id' => $allergenId,
            ]);
        }
    }

    /* -------------------------------------------------- */
    /* activation */
    /* -------------------------------------------------- */

    /* Compte les menus actifs utilisant le plat. */
    public static function countActiveMenus(int $id): int
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT COUNT(*)
            FROM menu_dish
            INNER JOIN menus
                ON menus.id = menu_dish.menu_id
            WHERE menu_dish.dish_id = :dish_id
            AND menus.is_active = TRUE
        ';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            'dish_id' => $id,
        ]);

        return (int) $statement->fetchColumn();
    }

    /* Active ou désactive un plat. */
    public static function setActive(
        int $id,
        bool $isActive
    ): bool {
        $pdo = Database::getConnection();

        $sql = '
            UPDATE dishes
            SET is_active = :is_active
            WHERE id = :id
        ';

        $statement = $pdo->prepare($sql);

        return $statement->execute([
            'id' => $id,
            'is_active' => $isActive ? 1 : 0,
        ]);
    }

    /* -------------------------------------------------- */
    /* récupération de la photo */
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
