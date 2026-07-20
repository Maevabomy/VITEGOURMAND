<?php

use App\Controllers\DishController;
use App\Controllers\EmployeeController;
use App\Controllers\HomeController;
use App\Controllers\MenuController;
use App\Controllers\AuthController;
use App\Controllers\OrderController;
use App\Controllers\UserController;

/* -------------------------------------------------- */
/* routes publiques */
/* -------------------------------------------------- */

/* Affiche la page d'accueil. */

$router->get('/', [HomeController::class, 'index']);

/* Affiche la liste des menus. */
$router->get('/menus', [MenuController::class, 'index']);

/* Affiche le détail d'un menu. */
$router->get('/menus/detail', [MenuController::class, 'show']);

/* Affiche le formulaire de commande. */
$router->get('/order/create', [OrderController::class, 'create']);

/* Recherche une adresse de livraison. */
$router->get('/order/address-search', [OrderController::class, 'searchAddress']);

/* Calcule la distance routière de livraison. */
$router->get('/order/delivery-distance', [OrderController::class,'calculateDistance',]);

/* Reçoit et valide le formulaire de commande. */
$router->post('/order/store', [OrderController::class,'store',]);

/* Affiche la confirmation d’une commande. */
$router->get('/order/confirmation', [OrderController::class,'confirmation',]);

/* Affiche le formulaire d'inscription. */
$router->get('/register', [AuthController::class, 'register']);

/* Traite le formulaire d'inscription. */
$router->post('/register', [AuthController::class, 'store']);

/* Affiche le formulaire de connexion. */
$router->get('/login', [AuthController::class, 'login']);

/* Traite la connexion. */
$router->post('/login', [AuthController::class, 'authenticate']);

/* Affiche le formulaire de mot de passe oublié. */
$router->get('/forgot-password', [AuthController::class, 'forgotPassword']);

/* Traite la demande de réinitialisation. */
$router->post('/forgot-password', [AuthController::class, 'sendResetLink']);

/* Affiche le formulaire de nouveau mot de passe. */
$router->get('/reset-password', [AuthController::class, 'resetPassword']);

/* Enregistre le nouveau mot de passe. */
$router->post('/reset-password', [AuthController::class, 'updatePassword']);

/* Déconnecte l'utilisateur. */
$router->post('/logout', [AuthController::class, 'logout']);

/* Affiche le tableau de bord de l'utilisateur. */
$router->get('/user/dashboard', [UserController::class, 'dashboard']);

/* Affiche le détail d'une commande. */
$router->get('/user/order/detail', [UserController::class, 'orderDetail']);

/* Affiche le formulaire de modification d'une commande. */
$router->get('/user/order/edit', [UserController::class, 'editOrder']);

/* Enregistre la modification d'une commande. */
$router->post('/user/order/update', [UserController::class, 'updateOrder']);

/* Annule une commande encore en attente. */
$router->post('/user/order/cancel', [UserController::class,'cancelOrder',]);

/* Enregistre l'avis d'une commande terminée. */
$router->post('/user/order/review', [UserController::class, 'storeReview']);

/* Affiche le formulaire de modification du profil. */
$router->get('/user/profile/edit', [UserController::class,'editProfile',]);

/* Enregistre les modifications du profil. */
$router->post('/user/profile/update', [UserController::class,'updateProfile',]);

/* Affiche le tableau de bord des employés. */
$router->get('/employee/dashboard',[EmployeeController::class, 'dashboard']);

/* Affiche le détail d'une commande pour un employé. */
$router->get('/employee/order/detail',[EmployeeController::class, 'orderDetail']);

/* Met à jour le statut d'une commande. */
$router->post('/employee/order/status',[EmployeeController::class, 'updateOrderStatus']);

/* Annule une commande après contact avec le client. */
$router->post('/employee/order/cancel',[EmployeeController::class, 'cancelOrder']);

/* Affiche une photo stockée dans MariaDB. */
$router->get('/dish/image', [DishController::class, 'image']);
