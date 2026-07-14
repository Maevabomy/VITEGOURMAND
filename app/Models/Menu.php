<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class Menu
{
    /* -------------------------------------------------- */
    /* récupération des menus */
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

    /* -------------------------------------------------- */
    /* récupération du détail d'un menu */
    /* -------------------------------------------------- */

    /* Récupère un menu avec ses plats et leurs allergènes. */
    public static function findByIdWithDishesAndAllergens(int $id): ?array
    {
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
    /* organisation des résultats */
    /* -------------------------------------------------- */

    /* Regroupe les plats appartenant au même menu. */
    private static function groupMenusWithDishes(array $rows): array
    {
        $menus = [];

        foreach ($rows as $row) {
            $menuId = (int) $row['menu_id'];

            /* Crée le menu uniquement lors de sa première apparition. */
            if (!isset($menus[$menuId])) {
                $menus[$menuId] = [
                    'id' => $menuId,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'minimum_people' => (int) $row['minimum_people'],
                    'base_price' => (float) $row['base_price'],
                    'stock_quantity' => (int) $row['stock_quantity'],
                    'theme_name' => $row['theme_name'],
                    'dietary_type_name' => $row['dietary_type_name'],
                    'dishes' => [],
                ];
            }

            /* Ajoute le plat dans la galerie du menu. */
            $menus[$menuId]['dishes'][] = [
                'id' => (int) $row['dish_id'],
                'name' => $row['dish_name'],
                'dish_type' => $row['dish_type'],
            ];
        }

        return array_values($menus);
    }

    /* -------------------------------------------------- */
    /* organisation du détail d'un menu */
    /* -------------------------------------------------- */

    /* Regroupe les allergènes appartenant au même plat. */
    private static function groupMenuDetail(array $rows): array
    {
        $firstRow = $rows[0];

        $menu = [
            'id' => (int) $firstRow['menu_id'],
            'title' => $firstRow['title'],
            'description' => $firstRow['menu_description'],
            'conditions' => $firstRow['conditions'],
            'minimum_order_days' => (int) $firstRow['minimum_order_days'],
            'available_from' => $firstRow['available_from'],
            'available_until' => $firstRow['available_until'],
            'minimum_people' => (int) $firstRow['minimum_people'],
            'base_price' => (float) $firstRow['base_price'],
            'stock_quantity' => (int) $firstRow['stock_quantity'],
            'theme_name' => $firstRow['theme_name'],
            'dietary_type_name' => $firstRow['dietary_type_name'],
            'dishes' => [],
        ];

        foreach ($rows as $row) {
            $dishId = (int) $row['dish_id'];

            /* Crée le plat uniquement lors de sa première apparition. */
            if (!isset($menu['dishes'][$dishId])) {
                $menu['dishes'][$dishId] = [
                    'id' => $dishId,
                    'name' => $row['dish_name'],
                    'description' => $row['dish_description'],
                    'dish_type' => $row['dish_type'],
                    'allergens' => [],
                ];
            }

            /* Ajoute l'allergène uniquement lorsqu'il existe. */
            if (!empty($row['allergen_id'])) {
                $menu['dishes'][$dishId]['allergens'][] = [
                    'id' => (int) $row['allergen_id'],
                    'name' => $row['allergen_name'],
                ];
            }
        }

        /* Réinitialise les numéros du tableau des plats. */
        $menu['dishes'] = array_values($menu['dishes']);

        return $menu;
    }
}
