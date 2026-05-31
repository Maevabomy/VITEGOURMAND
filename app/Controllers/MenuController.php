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
    /* -------------------------------------------------- */
    /* affichage du détail d'un menu */
    /* -------------------------------------------------- */

    /* Prépare les informations affichées dans la fiche d'un menu. */
    public function show(): void
    {
        $menuId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        /* Arrête l'affichage si l'identifiant est incorrect. */
        if (!$menuId || $menuId < 1) {
            http_response_code(404);

            echo '<h1>Erreur 404</h1>';
            echo '<p>Le menu demandé est introuvable.</p>';

            return;
        }

        /* Récupère le menu avec ses plats et leurs allergènes. */
        $menu = Menu::findByIdWithDishesAndAllergens($menuId);

        /* Arrête l'affichage si le menu n'existe pas. */
        if ($menu === null) {
            http_response_code(404);

            echo '<h1>Erreur 404</h1>';
            echo '<p>Le menu demandé est introuvable.</p>';

            return;
        }

        $pageTitle = $menu['title'];

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/menus/show.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}
