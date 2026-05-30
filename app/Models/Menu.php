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
}