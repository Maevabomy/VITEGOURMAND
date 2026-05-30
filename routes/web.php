<?php

use App\Controllers\DishController;
use App\Controllers\HomeController;
use App\Controllers\MenuController;

/* -------------------------------------------------- */
/* routes publiques */
/* -------------------------------------------------- */

/* Affiche la page d'accueil. */
$router->get('/', [HomeController::class, 'index']);

/* Affiche la liste des menus. */
$router->get('/menus', [MenuController::class, 'index']);

/* Affiche une photo stockée dans MariaDB. */
$router->get('/dish/image', [DishController::class, 'image']);