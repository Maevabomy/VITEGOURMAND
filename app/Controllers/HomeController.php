<?php

namespace App\Controllers;

use App\Models\OpeningHour;
use App\Models\Review;

class HomeController
{
    /* -------------------------------------------------- */
    /* affichage de la page d'accueil */
    /* -------------------------------------------------- */

    /* Prépare les données nécessaires à la page d'accueil. */
    public function index(): void
    {
        $pageTitle = 'Accueil';

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Récupère les avis validés affichés sur la page d'accueil. */
        $reviews = Review::getApprovedReviews();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/home/home.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}