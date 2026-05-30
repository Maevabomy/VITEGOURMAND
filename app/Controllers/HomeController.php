<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $pageTitle = 'Accueil';

        $view = BASE_PATH . '/app/Views/home/home.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}