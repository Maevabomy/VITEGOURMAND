<?php

use App\Controllers\DishController;
use App\Controllers\HomeController;
use App\Controllers\MenuController;
use App\Controllers\AuthController;

/* -------------------------------------------------- */
/* routes publiques */
/* -------------------------------------------------- */

/* Affiche la page d'accueil. */
$router->get('/', [HomeController::class, 'index']);

/* Affiche la liste des menus. */
$router->get('/menus', [MenuController::class, 'index']);

/* Affiche le détail d'un menu. */
$router->get('/menus/detail', [MenuController::class, 'show']);

/* Affiche le formulaire d'inscription. */
$router->get('/register', [AuthController::class, 'register']);

/* Affiche une photo stockée dans MariaDB. */
$router->get('/dish/image', [DishController::class, 'image']);

