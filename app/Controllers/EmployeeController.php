<?php

namespace App\Controllers;

use App\Models\OpeningHour;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Services\MailService;


class EmployeeController
{
    /* -------------------------------------------------- */
    /* tableau de bord employé */
    /* -------------------------------------------------- */

    /* Affiche les commandes et les filtres de l'espace employé. */
    public function dashboard(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/employee/dashboard';

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];

        /* Vérifie que le compte existe et reste actif. */
        $user = User::findById($userId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        /* Autorise les employés et l'administrateur. */
        if (
            $user['role'] !== 'employee'
            && $user['role'] !== 'admin'
        ) {
            http_response_code(403);

            $pageTitle = 'Accès refusé';
            $errorTitle = 'Accès refusé';
            $errorMessage =
                'Vous ne pouvez pas accéder à l’espace employé.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Récupère et nettoie les filtres. */
        $statusId = filter_input(
            INPUT_GET,
            'status',
            FILTER_VALIDATE_INT
        );

        if (
            $statusId === false
            || $statusId === null
            || $statusId < 1
        ) {
            $statusId = null;
        }

        $search = trim(
            (string) ($_GET['search'] ?? '')
        );

        if (mb_strlen($search) > 100) {
            $search = mb_substr($search, 0, 100);
        }

        /* Récupère les données affichées sur le tableau de bord. */
        $statuses = Order::findAllStatuses();

        $orders = Order::findAllForEmployee(
            $statusId,
            $search
        );

        $pageTitle = 'Espace employé';

        $openingHours = OpeningHour::getAll();

        $view =
            BASE_PATH . '/app/Views/employee/dashboard.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* -------------------------------------------------- */
    /* détail d'une commande */
    /* -------------------------------------------------- */

    /* Affiche le détail complet d'une commande. */
    public function orderDetail(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/employee/dashboard';

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];

        /* Vérifie que le compte existe toujours. */
        $user = User::findById($userId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        /* Autorise les employés et l'administrateur. */
        if (
            $user['role'] !== 'employee'
            && $user['role'] !== 'admin'
        ) {
            http_response_code(403);

            $pageTitle = 'Accès refusé';
            $errorTitle = 'Accès refusé';
            $errorMessage =
                'Vous ne pouvez pas accéder à l’espace employé.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Vérifie l'identifiant de la commande. */
        $orderId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (
            $orderId === false
            || $orderId === null
            || $orderId < 1
        ) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Récupère la commande et son historique. */
        $order = Order::findByIdForEmployee(
            (int) $orderId
        );

        if ($order === null) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        $statusHistory =
            Order::findStatusHistoryForEmployee(
                (int) $orderId
            );

        /* Prépare les changements de statut autorisés. */
        $allowedNextStatuses =
            Order::getAllowedNextStatuses(
                $order['status_name']
            );

        /* Crée le jeton du formulaire de statut. */
        if (empty($_SESSION['employee_status_csrf_token'])) {
            $_SESSION['employee_status_csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        $statusCsrfToken =
            $_SESSION['employee_status_csrf_token'];

        /* Crée le jeton du formulaire d'annulation. */
        if (empty($_SESSION['employee_cancel_csrf_token'])) {
            $_SESSION['employee_cancel_csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        $cancelCsrfToken =
            $_SESSION['employee_cancel_csrf_token'];

        /* Autorise l'annulation uniquement avant livraison. */
        $nonCancellableStatuses = [
            'Livrée',
            'En attente du retour de matériel',
            'Terminée',
            'Annulée',
        ];

        $canCancelOrder = !in_array(
            $order['status_name'],
            $nonCancellableStatuses,
            true
        );

        /* Récupère les messages après redirection. */
        $success =
            $_SESSION['employee_success'] ?? null;

        $error =
            $_SESSION['employee_error'] ?? null;

        unset(
            $_SESSION['employee_success'],
            $_SESSION['employee_error']
        );

        $pageTitle = 'Détail de la commande';

        $openingHours = OpeningHour::getAll();

        $view =
            BASE_PATH . '/app/Views/employee/order-detail.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Met à jour le statut d'une commande. */
    public function updateOrderStatus(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $employeeId = (int) $_SESSION['user']['id'];

        $user = User::findById($employeeId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        /* Autorise les employés et l'administrateur. */
        if (
            $user['role'] !== 'employee'
            && $user['role'] !== 'admin'
        ) {
            http_response_code(403);

            echo 'Accès refusé.';

            return;
        }

        $orderId = filter_input(
            INPUT_POST,
            'order_id',
            FILTER_VALIDATE_INT
        );

        if (!$orderId || $orderId < 1) {
            http_response_code(404);

            echo 'Commande introuvable.';

            return;
        }

        /* Vérifie le jeton CSRF. */
        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['employee_status_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_status_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/order/detail?id='
                    . (int) $orderId
            );

            exit;
        }

        $newStatus = trim(
            (string) ($_POST['new_status'] ?? '')
        );

        $note = trim(
            (string) ($_POST['note'] ?? '')
        );

        if ($newStatus === '') {
            $_SESSION['employee_error'] =
                'Veuillez sélectionner le nouveau statut.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/order/detail?id='
                    . (int) $orderId
            );

            exit;
        }

        if (mb_strlen($note) > 1000) {
            $_SESSION['employee_error'] =
                'La note ne peut pas dépasser 1 000 caractères.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/order/detail?id='
                    . (int) $orderId
            );

            exit;
        }

        $result = Order::updateStatusByEmployee(
            (int) $orderId,
            $employeeId,
            $newStatus,
            $note
        );

        if ($result === 'updated') {
            unset(
                $_SESSION['employee_status_csrf_token']
            );

            /*
             * La transaction est terminée avant l'envoi.
             * Un échec du serveur mail ne bloque pas la commande.
             */
            $updatedOrder =
                Order::findByIdForEmployee(
                    (int) $orderId
                );

            if ($updatedOrder !== null) {
                self::sendOrderStatusEmail(
                    $updatedOrder,
                    $newStatus
                );
            }

            $_SESSION['employee_success'] =
                'Le statut de la commande a bien été mis à jour.';
        } elseif ($result === 'invalid_transition') {
            $_SESSION['employee_error'] =
                'Ce changement de statut n’est pas autorisé.';
        } elseif ($result === 'conflict') {
            $_SESSION['employee_error'] =
                'La commande a été modifiée entre-temps. '
                . 'Veuillez vérifier son statut actuel.';
        } elseif ($result === 'not_found') {
            $_SESSION['employee_error'] =
                'La commande est introuvable.';
        } else {
            $_SESSION['employee_error'] =
                'Une erreur est survenue pendant la mise à jour.';
        }

        header(
            'Location: '
                . BASE_URL
                . '/employee/order/detail?id='
                . (int) $orderId
        );

        exit;
    }

    /* Envoie l'e-mail correspondant au nouveau statut. */
    private function sendOrderStatusEmail(
        array $order,
        string $newStatus
    ): void {
        $customerEmail =
            (string) $order['customer_email'];

        if ($newStatus === 'Acceptée') {
            MailService::sendOrderAcceptedEmail(
                $customerEmail,
                $order
            );

            return;
        }

        if (
            $newStatus
            === 'En attente du retour de matériel'
        ) {
            MailService::sendEquipmentReturnEmail(
                $customerEmail,
                $order
            );

            return;
        }

        if ($newStatus === 'Terminée') {
            $orderDetailLink =
                BASE_URL
                . '/user/order/detail?id='
                . (int) $order['id'];

            MailService::sendOrderCompletedEmail(
                $customerEmail,
                $order,
                $orderDetailLink
            );
        }
    }

    /* Annule une commande après contact avec le client. */
    public function cancelOrder(): void
    {
        /* Vérifie la connexion. */
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $employeeId = (int) $_SESSION['user']['id'];

        $user = User::findById($employeeId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        /* Autorise les employés et l'administrateur. */
        if (
            $user['role'] !== 'employee'
            && $user['role'] !== 'admin'
        ) {
            http_response_code(403);

            echo 'Accès refusé.';

            return;
        }

        $orderId = filter_input(
            INPUT_POST,
            'order_id',
            FILTER_VALIDATE_INT
        );

        if (!$orderId || $orderId < 1) {
            http_response_code(404);

            echo 'Commande introuvable.';

            return;
        }

        $detailUrl =
            BASE_URL
            . '/employee/order/detail?id='
            . (int) $orderId;

        /* Vérifie le jeton CSRF. */
        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['employee_cancel_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_cancel_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_error'] =
                'Le formulaire d’annulation a expiré.';

            header('Location: ' . $detailUrl);
            exit;
        }

        $contactConfirmed =
            isset($_POST['contact_confirmed'])
            && $_POST['contact_confirmed'] === '1';

        $contactMethod = trim(
            (string) ($_POST['contact_method'] ?? '')
        );

        $reason = trim(
            (string) ($_POST['cancellation_reason'] ?? '')
        );

        /* Le contact préalable doit être explicitement confirmé. */
        if (!$contactConfirmed) {
            $_SESSION['employee_error'] =
                'Vous devez confirmer avoir contacté le client.';

            header('Location: ' . $detailUrl);
            exit;
        }

        $allowedContactMethods = [
            'Téléphone',
            'E-mail',
        ];

        if (
            !in_array(
                $contactMethod,
                $allowedContactMethods,
                true
            )
        ) {
            $_SESSION['employee_error'] =
                'Sélectionnez un mode de contact valide.';

            header('Location: ' . $detailUrl);
            exit;
        }

        if ($reason === '') {
            $_SESSION['employee_error'] =
                'Le motif de l’annulation est obligatoire.';

            header('Location: ' . $detailUrl);
            exit;
        }

        if (mb_strlen($reason) < 10) {
            $_SESSION['employee_error'] =
                'Le motif doit contenir au moins 10 caractères.';

            header('Location: ' . $detailUrl);
            exit;
        }

        if (mb_strlen($reason) > 1000) {
            $_SESSION['employee_error'] =
                'Le motif ne peut pas dépasser 1 000 caractères.';

            header('Location: ' . $detailUrl);
            exit;
        }

        $result = Order::cancelByEmployee(
            (int) $orderId,
            $employeeId,
            $contactMethod,
            $reason
        );

        if ($result === 'cancelled') {
            unset(
                $_SESSION['employee_cancel_csrf_token']
            );

            /*
             * L'annulation est déjà validée en base.
             * Le mail est donc envoyé en dehors de la transaction.
             */
            $cancelledOrder =
                Order::findByIdForEmployee(
                    (int) $orderId
                );

            if ($cancelledOrder !== null) {
                MailService::sendOrderCancelledEmail(
                    $cancelledOrder['customer_email'],
                    $cancelledOrder
                );
            }

            $_SESSION['employee_success'] =
                'La commande a bien été annulée.';
        } elseif ($result === 'not_allowed') {
            $_SESSION['employee_error'] =
                'Cette commande ne peut plus être annulée.';
        } elseif ($result === 'invalid_contact') {
            $_SESSION['employee_error'] =
                'Le mode de contact est invalide.';
        } elseif ($result === 'invalid_reason') {
            $_SESSION['employee_error'] =
                'Le motif de l’annulation est obligatoire.';
        } elseif ($result === 'conflict') {
            $_SESSION['employee_error'] =
                'La commande a été modifiée entre-temps. '
                . 'Veuillez vérifier son statut.';
        } elseif ($result === 'not_found') {
            $_SESSION['employee_error'] =
                'La commande est introuvable.';
        } else {
            $_SESSION['employee_error'] =
                'Une erreur est survenue pendant l’annulation.';
        }

        header('Location: ' . $detailUrl);
        exit;
    }

        /* -------------------------------------------------- */
    /* modération des avis */
    /* -------------------------------------------------- */

    /* Affiche les avis clients en attente de validation. */
    public function reviews(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/employee/reviews';

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];

        $user = User::findById($userId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (
            $user['role'] !== 'employee'
            && $user['role'] !== 'admin'
        ) {
            http_response_code(403);

            $pageTitle = 'Accès refusé';
            $errorTitle = 'Accès refusé';
            $errorMessage =
                'Vous ne pouvez pas accéder à cet espace.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        $reviews = Review::findPendingForEmployee();

        if (empty($_SESSION['employee_review_csrf_token'])) {
            $_SESSION['employee_review_csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        $reviewCsrfToken =
            $_SESSION['employee_review_csrf_token'];

        $success =
            $_SESSION['employee_review_success'] ?? null;

        $error =
            $_SESSION['employee_review_error'] ?? null;

        unset(
            $_SESSION['employee_review_success'],
            $_SESSION['employee_review_error']
        );

        $pageTitle = 'Modération des avis';

        $openingHours = OpeningHour::getAll();

        $view =
            BASE_PATH . '/app/Views/employee/reviews.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Approuve ou refuse un avis encore en attente. */
    public function moderateReview(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];

        $user = User::findById($userId);

        if (
            $user === null
            || (
                $user['role'] !== 'employee'
                && $user['role'] !== 'admin'
            )
        ) {
            http_response_code(403);

            echo 'Accès refusé.';

            return;
        }

        $reviewId = filter_input(
            INPUT_POST,
            'review_id',
            FILTER_VALIDATE_INT
        );

        $moderationStatus = trim(
            (string) ($_POST['moderation_status'] ?? '')
        );

        $csrfToken = trim(
            (string) ($_POST['csrf_token'] ?? '')
        );

        if (
            empty($_SESSION['employee_review_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_review_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_review_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header('Location: ' . BASE_URL . '/employee/reviews');
            exit;
        }

        if (!$reviewId || $reviewId < 1) {
            $_SESSION['employee_review_error'] =
                'L’avis sélectionné est introuvable.';

            header('Location: ' . BASE_URL . '/employee/reviews');
            exit;
        }

        if (
            !in_array(
                $moderationStatus,
                ['approved', 'refused'],
                true
            )
        ) {
            $_SESSION['employee_review_error'] =
                'L’action demandée est invalide.';

            header('Location: ' . BASE_URL . '/employee/reviews');
            exit;
        }

        $moderated = Review::moderateByEmployee(
            (int) $reviewId,
            $moderationStatus
        );

        if ($moderated) {
            unset($_SESSION['employee_review_csrf_token']);

            $_SESSION['employee_review_success'] =
                $moderationStatus === 'approved'
                    ? 'L’avis a été approuvé.'
                    : 'L’avis a été refusé.';
        } else {
            $_SESSION['employee_review_error'] =
                'Cet avis a déjà été traité ou n’existe plus.';
        }

        header('Location: ' . BASE_URL . '/employee/reviews');
        exit;
    }
}
