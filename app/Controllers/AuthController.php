<?php

namespace App\Controllers;

use App\Models\OpeningHour;

class AuthController
{
    /* -------------------------------------------------- */
    /* affichage de l'inscription */
    /* -------------------------------------------------- */

    /* Prépare la page de création de compte. */
    public function register(): void
    {
        $pageTitle = 'Créer un compte';

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/auth/register.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}