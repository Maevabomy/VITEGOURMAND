<?php

namespace App\Controllers;

use App\Models\Menu;
use App\Models\OpeningHour;

class MenuController
{
    /* -------------------------------------------------- */
    /* affichage de la liste des menus */
    /* -------------------------------------------------- */

    /* Prépare les menus affichés dans le catalogue. */
    public function index(): void
    {
        $pageTitle = 'Nos menus';

        /* Récupère les menus actifs avec leurs plats. */
        $menus = Menu::getAllWithDishes();

        /* Prépare les listes utilisées dans les filtres. */
        $themes = [];
        $dietaryTypes = [];

        /* Récupère les thèmes et régimes présents dans les menus. */
        foreach ($menus as $menu) {
            $themes[] = $menu['theme_name'];
            $dietaryTypes[] = $menu['dietary_type_name'];
        }

        /* Supprime les doublons. */
        $themes = array_unique($themes);
        $dietaryTypes = array_unique($dietaryTypes);

        /* Trie les listes par ordre alphabétique. */
        sort($themes);
        sort($dietaryTypes);

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/menus/index.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}