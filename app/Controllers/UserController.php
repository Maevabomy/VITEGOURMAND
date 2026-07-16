<?php

namespace App\Controllers;

use App\Models\OpeningHour;
use App\Models\Order;
use App\Models\User;

class UserController
{
    /* -------------------------------------------------- */
    /* tableau de bord utilisateur */
    /* -------------------------------------------------- */

    /* Affiche le compte et les commandes de l'utilisateur. */
    public function dashboard(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/user/dashboard';

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];

        /* Récupère les informations actuelles du compte. */
        $user = User::findById($userId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        /* Limite cette page aux comptes utilisateurs. */
        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas accéder à cet espace.</p>';

            return;
        }

        /* Récupère les commandes du client. */
        $orders = Order::findAllByUserId($userId);

        $pageTitle = 'Mon compte';

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/user/dashboard.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}