<?php

namespace App\Models;

use App\Services\Database;
use PDO;
use Throwable;

class Menu
{
    /* -------------------------------------------------- */
    /* récupération publique des menus */
    /* -------------------------------------------------- */

    /* Récupère les menus actifs avec leurs plats. */
    public static function getAllWithDishes(): array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                menus.id AS menu_id,
                menus.title,
                menus.description,
                menus.minimum_people,
                menus.base_price,
                menus.stock_quantity,
                themes.name AS theme_name,
                dietary_types.name AS dietary_type_name,
                dishes.id AS dish_id,
                dishes.name AS dish_name,
                dishes.dish_type
            FROM menus
            INNER JOIN themes
                ON themes.id = menus.theme_id
            INNER JOIN dietary_types
                ON dietary_types.id = menus.dietary_type_id
            INNER JOIN menu_dish
                ON menu_dish.menu_id = menus.id
            INNER JOIN dishes
                ON dishes.id = menu_dish.dish_id
            WHERE menus.is_active = TRUE
            AND dishes.is_active = TRUE
            ORDER BY menus.id, dishes.id
        ';

        $statement = $pdo->query($sql);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return self::groupMenusWithDishes($rows);
    }

    /* Récupère un menu avec ses plats et allergènes. */
    public static function findByIdWithDishesAndAllergens(
        int $id
    ): ?array {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                menus.id AS menu_id,
                menus.title,
                menus.description AS menu_description,
                menus.conditions,
                menus.minimum_order_days,
                menus.available_from,
                menus.available_until,
                menus.minimum_people,
                menus.base_price,
                menus.stock_quantity,
                themes.name AS theme_name,
                dietary_types.name AS dietary_type_name,
                dishes.id AS dish_id,
                dishes.name AS dish_name,
                dishes.description AS dish_description,
                dishes.dish_type,
                allergens.id AS allergen_id,
                allergens.name AS allergen_name
            FROM menus
            INNER JOIN themes
                ON themes.id = menus.theme_id
            INNER JOIN dietary_types
                ON dietary_types.id = menus.dietary_type_id
            INNER JOIN menu_dish
                ON menu_dish.menu_id = menus.id
            INNER JOIN dishes
                ON dishes.id = menu_dish.dish_id
            LEFT JOIN dish_allergen
                ON dish_allergen.dish_id = dishes.id
            LEFT JOIN allergens
                ON allergens.id = dish_allergen.allergen_id
            WHERE menus.id = :id
            AND menus.is_active = TRUE
            AND dishes.is_active = TRUE
            ORDER BY
                FIELD(
                    dishes.dish_type,
                    "starter",
                    "main_course",
                    "dessert"
                ),
                allergens.name
        ';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        return self::groupMenuDetail($rows);
    }

    /* -------------------------------------------------- */
    /* récupération employé */
    /* -------------------------------------------------- */

    /* Récupère tous les menus pour l'espace employé. */
    public static function getAllForEmployee(): array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                menus.id,
                menus.title,
                menus.description,
                menus.minimum_order_days,
                menus.available_from,
                menus.available_until,
                menus.minimum_people,
                menus.base_price,
                menus.stock_quantity,
                menus.is_active,
                themes.name AS theme_name,
                dietary_types.name AS dietary_type_name,
                COUNT(DISTINCT menu_dish.dish_id) AS dish_count,
                GROUP_CONCAT(
                    DISTINCT dishes.name
                    ORDER BY
                        FIELD(
                            dishes.dish_type,
                            "starter",
                            "main_course",
                            "dessert"
                        ),
                        dishes.name
                    SEPARATOR ", "
                ) AS dish_names
            FROM menus
            INNER JOIN themes
                ON themes.id = menus.theme_id
            INNER JOIN dietary_types
                ON dietary_types.id = menus.dietary_type_id
            LEFT JOIN menu_dish
                ON menu_dish.menu_id = menus.id
            LEFT JOIN dishes
                ON dishes.id = menu_dish.dish_id
            GROUP BY
                menus.id,
                menus.title,
                menus.description,
                menus.minimum_order_days,
                menus.available_from,
                menus.available_until,
                menus.minimum_people,
                menus.base_price,
                menus.stock_quantity,
                menus.is_active,
                themes.name,
                dietary_types.name
            ORDER BY
                menus.is_active DESC,
                menus.title
        ';

        $statement = $pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Récupère un menu pour le formulaire employé. */
    public static function findForEmployee(int $id): ?array
    {
        $pdo = Database::getConnection();

        $sql = '
            SELECT
                id,
                theme_id,
                dietary_type_id,
                title,
                description,
                conditions,
                minimum_order_days,
                available_from,
                available_until,
                minimum_people,
                base_price,
                stock_quantity,
                is_active
            FROM menus
            WHERE id = :id
        ';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        $menu = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$menu) {
            return null;
        }

        $dishStatement = $pdo->prepare(
            '
                SELECT dish_id
                FROM menu_dish
                WHERE menu_id = :menu_id
                ORDER BY dish_id
            '
        );

        $dishStatement->execute([
            'menu_id' => $id,
        ]);

        $menu['dish_ids'] = array_map(
            'intval',
            $dishStatement->fetchAll(PDO::FETCH_COLUMN)
        );

        return $menu;
    }

    /* Récupère les thèmes disponibles. */
    public static function getThemes(): array
    {
        $pdo = Database::getConnection();

        $statement = $pdo->query(
            '
                SELECT id, name
                FROM themes
                ORDER BY name
            '
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Récupère les régimes alimentaires. */
    public static function getDietaryTypes(): array
    {
        $pdo = Database::getConnection();

        $statement = $pdo->query(
            '
                SELECT id, name
                FROM dietary_types
                ORDER BY name
            '
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------------------------- */
    /* création et modification */
    /* -------------------------------------------------- */

    /* Crée un menu et associe ses plats. */
    public static function create(
        array $data,
        array $dishIds
    ): bool {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $sql = '
                INSERT INTO menus (
                    theme_id,
                    dietary_type_id,
                    title,
                    description,
                    conditions,
                    minimum_order_days,
                    available_from,
                    available_until,
                    minimum_people,
                    base_price,
                    stock_quantity,
                    is_active
                ) VALUES (
                    :theme_id,
                    :dietary_type_id,
                    :title,
                    :description,
                    :conditions,
                    :minimum_order_days,
                    :available_from,
                    :available_until,
                    :minimum_people,
                    :base_price,
                    :stock_quantity,
                    TRUE
                )
            ';

            $statement = $pdo->prepare($sql);

            $statement->execute([
                'theme_id' => $data['theme_id'],
                'dietary_type_id' =>
                $data['dietary_type_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'conditions' => $data['conditions'],
                'minimum_order_days' =>
                $data['minimum_order_days'],
                'available_from' =>
                $data['available_from'],
                'available_until' =>
                $data['available_until'],
                'minimum_people' =>
                $data['minimum_people'],
                'base_price' => $data['base_price'],
                'stock_quantity' =>
                $data['stock_quantity'],
            ]);

            $menuId = (int) $pdo->lastInsertId();

            self::replaceDishes(
                $pdo,
                $menuId,
                $dishIds
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

    /* Modifie un menu et ses plats. */
    public static function update(
        int $id,
        array $data,
        array $dishIds
    ): bool {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $sql = '
                UPDATE menus
                SET
                    theme_id = :theme_id,
                    dietary_type_id = :dietary_type_id,
                    title = :title,
                    description = :description,
                    conditions = :conditions,
                    minimum_order_days = :minimum_order_days,
                    available_from = :available_from,
                    available_until = :available_until,
                    minimum_people = :minimum_people,
                    base_price = :base_price,
                    stock_quantity = :stock_quantity
                WHERE id = :id
            ';

            $statement = $pdo->prepare($sql);

            $statement->execute([
                'id' => $id,
                'theme_id' => $data['theme_id'],
                'dietary_type_id' =>
                $data['dietary_type_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'conditions' => $data['conditions'],
                'minimum_order_days' =>
                $data['minimum_order_days'],
                'available_from' =>
                $data['available_from'],
                'available_until' =>
                $data['available_until'],
                'minimum_people' =>
                $data['minimum_people'],
                'base_price' => $data['base_price'],
                'stock_quantity' =>
                $data['stock_quantity'],
            ]);

            self::replaceDishes(
                $pdo,
                $id,
                $dishIds
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

    /* Remplace les plats associés au menu. */
    private static function replaceDishes(
        PDO $pdo,
        int $menuId,
        array $dishIds
    ): void {
        $deleteStatement = $pdo->prepare(
            '
                DELETE FROM menu_dish
                WHERE menu_id = :menu_id
            '
        );

        $deleteStatement->execute([
            'menu_id' => $menuId,
        ]);

        $insertStatement = $pdo->prepare(
            '
                INSERT INTO menu_dish (
                    menu_id,
                    dish_id
                ) VALUES (
                    :menu_id,
                    :dish_id
                )
            '
        );

        foreach ($dishIds as $dishId) {
            $insertStatement->execute([
                'menu_id' => $menuId,
                'dish_id' => $dishId,
            ]);
        }
    }

    /* -------------------------------------------------- */
    /* activation */
    /* -------------------------------------------------- */

    /* Active ou désactive un menu. */
    public static function setActive(
        int $id,
        bool $isActive
    ): bool {
        $pdo = Database::getConnection();

        $statement = $pdo->prepare(
            '
                UPDATE menus
                SET is_active = :is_active
                WHERE id = :id
            '
        );

        return $statement->execute([
            'id' => $id,
            'is_active' => $isActive ? 1 : 0,
        ]);
    }

    /* Vérifie que le menu possède les trois types de plats actifs. */
    public static function hasCompleteActiveComposition(
        int $id
    ): bool {
        $pdo = Database::getConnection();

        $statement = $pdo->prepare(
            '
                SELECT COUNT(DISTINCT dishes.dish_type)
                FROM menu_dish
                INNER JOIN dishes
                    ON dishes.id = menu_dish.dish_id
                WHERE menu_dish.menu_id = :menu_id
                AND dishes.is_active = TRUE
            '
        );

        $statement->execute([
            'menu_id' => $id,
        ]);

        return (int) $statement->fetchColumn() === 3;
    }

    /* -------------------------------------------------- */
    /* organisation des résultats publics */
    /* -------------------------------------------------- */

    /* Regroupe les plats appartenant au même menu. */
    private static function groupMenusWithDishes(
        array $rows
    ): array {
        $menus = [];

        foreach ($rows as $row) {
            $menuId = (int) $row['menu_id'];

            if (!isset($menus[$menuId])) {
                $menus[$menuId] = [
                    'id' => $menuId,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'minimum_people' =>
                    (int) $row['minimum_people'],
                    'base_price' =>
                    (float) $row['base_price'],
                    'stock_quantity' =>
                    (int) $row['stock_quantity'],
                    'theme_name' => $row['theme_name'],
                    'dietary_type_name' =>
                    $row['dietary_type_name'],
                    'dishes' => [],
                ];
            }

            $menus[$menuId]['dishes'][] = [
                'id' => (int) $row['dish_id'],
                'name' => $row['dish_name'],
                'dish_type' => $row['dish_type'],
            ];
        }

        return array_values($menus);
    }

    /* Regroupe les allergènes appartenant au même plat. */
    private static function groupMenuDetail(
        array $rows
    ): array {
        $firstRow = $rows[0];

        $menu = [
            'id' => (int) $firstRow['menu_id'],
            'title' => $firstRow['title'],
            'description' =>
            $firstRow['menu_description'],
            'conditions' => $firstRow['conditions'],
            'minimum_order_days' =>
            (int) $firstRow['minimum_order_days'],
            'available_from' =>
            $firstRow['available_from'],
            'available_until' =>
            $firstRow['available_until'],
            'minimum_people' =>
            (int) $firstRow['minimum_people'],
            'base_price' =>
            (float) $firstRow['base_price'],
            'stock_quantity' =>
            (int) $firstRow['stock_quantity'],
            'theme_name' => $firstRow['theme_name'],
            'dietary_type_name' =>
            $firstRow['dietary_type_name'],
            'dishes' => [],
        ];

        foreach ($rows as $row) {
            $dishId = (int) $row['dish_id'];

            if (!isset($menu['dishes'][$dishId])) {
                $menu['dishes'][$dishId] = [
                    'id' => $dishId,
                    'name' => $row['dish_name'],
                    'description' =>
                    $row['dish_description'],
                    'dish_type' => $row['dish_type'],
                    'allergens' => [],
                ];
            }

            if (!empty($row['allergen_id'])) {
                $menu['dishes'][$dishId]['allergens'][] = [
                    'id' => (int) $row['allergen_id'],
                    'name' => $row['allergen_name'],
                ];
            }
        }

        $menu['dishes'] =
            array_values($menu['dishes']);

        return $menu;
    }
}
