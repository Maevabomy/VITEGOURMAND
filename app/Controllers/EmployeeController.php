<?php

namespace App\Controllers;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\OpeningHour;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Services\MailService;


class EmployeeController
{

    /* -------------------------------------------------- */
    /* accueil employé */
    /* -------------------------------------------------- */

    /* Affiche l'accueil de l'espace professionnel. */
    public function home(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/employee';

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

        $pageTitle = 'Espace professionnel';

        $openingHours = OpeningHour::getAll();

        $view =
            BASE_PATH . '/app/Views/employee/home.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* -------------------------------------------------- */
    /* gestion des horaires */
    /* -------------------------------------------------- */

    /* Affiche les horaires dans l'espace professionnel. */
    public function openingHours(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/employee/opening-hours';

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

        /* Crée le jeton de sécurité du formulaire. */
        if (empty($_SESSION['employee_hours_csrf_token'])) {
            $_SESSION['employee_hours_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['employee_hours_csrf_token'];

        $openingHours = OpeningHour::getAll();

        $success =
            $_SESSION['employee_hours_success'] ?? null;

        $error =
            $_SESSION['employee_hours_error'] ?? null;

        unset(
            $_SESSION['employee_hours_success'],
            $_SESSION['employee_hours_error']
        );

        $pageTitle = 'Gestion des horaires';

        $view =
            BASE_PATH
            . '/app/Views/employee/opening-hours.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Enregistre les horaires de la semaine. */
    public function updateOpeningHours(): void
    {
        /* Vérifie la connexion. */
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];

        $user = User::findById($userId);

        /* Vérifie le rôle professionnel. */
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

        $csrfToken = trim(
            (string) ($_POST['csrf_token'] ?? '')
        );

        /* Vérifie le jeton CSRF. */
        if (
            empty($_SESSION['employee_hours_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_hours_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_hours_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/opening-hours'
            );
            exit;
        }

        $submittedHours =
            $_POST['opening_hours'] ?? null;

        if (!is_array($submittedHours)) {
            $_SESSION['employee_hours_error'] =
                'Les horaires envoyés sont invalides.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/opening-hours'
            );
            exit;
        }

        $currentHours = OpeningHour::getAll();

        /* Vérifie que les sept jours sont présents. */
        if (count($submittedHours) !== count($currentHours)) {
            $_SESSION['employee_hours_error'] =
                'Tous les jours de la semaine doivent être renseignés.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/opening-hours'
            );
            exit;
        }

        $validatedHours = [];

        foreach ($currentHours as $currentHour) {
            $dayNumber =
                (int) $currentHour['day_number'];

            $submittedDay =
                $submittedHours[$dayNumber] ?? null;

            if (!is_array($submittedDay)) {
                $_SESSION['employee_hours_error'] =
                    'Un jour de la semaine est manquant.';

                header(
                    'Location: '
                        . BASE_URL
                        . '/employee/opening-hours'
                );
                exit;
            }

            $isClosed =
                isset($submittedDay['is_closed'])
                && $submittedDay['is_closed'] === '1';

            $openingTime = trim(
                (string) (
                    $submittedDay['opening_time'] ?? ''
                )
            );

            $closingTime = trim(
                (string) (
                    $submittedDay['closing_time'] ?? ''
                )
            );

            if ($isClosed) {
                $openingTime = null;
                $closingTime = null;
            } else {
                if (
                    !$this->isValidTime($openingTime)
                    || !$this->isValidTime($closingTime)
                ) {
                    $_SESSION['employee_hours_error'] =
                        'Les horaires de '
                        . $currentHour['day_name']
                        . ' sont invalides.';

                    header(
                        'Location: '
                            . BASE_URL
                            . '/employee/opening-hours'
                    );
                    exit;
                }

                if ($openingTime >= $closingTime) {
                    $_SESSION['employee_hours_error'] =
                        'L’heure de fermeture de '
                        . $currentHour['day_name']
                        . ' doit être postérieure à l’heure d’ouverture.';

                    header(
                        'Location: '
                            . BASE_URL
                            . '/employee/opening-hours'
                    );
                    exit;
                }
            }

            $validatedHours[] = [
                'day_number' => $dayNumber,
                'opening_time' => $openingTime,
                'closing_time' => $closingTime,
                'is_closed' => $isClosed ? 1 : 0,
            ];
        }

        $updated =
            OpeningHour::updateWeek($validatedHours);

        if (!$updated) {
            $_SESSION['employee_hours_error'] =
                'Une erreur est survenue pendant la mise à jour.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/opening-hours'
            );
            exit;
        }

        unset($_SESSION['employee_hours_csrf_token']);

        $_SESSION['employee_hours_success'] =
            'Les horaires ont bien été mis à jour.';

        header(
            'Location: '
                . BASE_URL
                . '/employee/opening-hours'
        );
        exit;
    }

    /* Vérifie le format d'une heure. */
    private function isValidTime(string $time): bool
    {
        if (
            !preg_match(
                '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $time
            )
        ) {
            return false;
        }

        return true;
    }

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
    /* gestion des menus */
    /* -------------------------------------------------- */

    /* Affiche tous les menus dans l'espace employé. */
    public function menus(): void
    {
        $this->requireProfessionalUser(
            '/employee/menus'
        );

        if (empty($_SESSION['employee_menu_csrf_token'])) {
            $_SESSION['employee_menu_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['employee_menu_csrf_token'];

        $menus = Menu::getAllForEmployee();

        $success =
            $_SESSION['employee_menu_success'] ?? null;

        $error =
            $_SESSION['employee_menu_error'] ?? null;

        unset(
            $_SESSION['employee_menu_success'],
            $_SESSION['employee_menu_error']
        );

        $openingHours = OpeningHour::getAll();

        $pageTitle = 'Gestion des menus';

        $view =
            BASE_PATH . '/app/Views/employee/menus.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Affiche le formulaire de création ou modification. */
    public function menuForm(): void
    {
        $this->requireProfessionalUser(
            '/employee/menus'
        );

        if (empty($_SESSION['employee_menu_csrf_token'])) {
            $_SESSION['employee_menu_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['employee_menu_csrf_token'];

        $menuId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $menu = null;

        if ($menuId) {
            $menu = Menu::findForEmployee($menuId);

            if ($menu === null) {
                $_SESSION['employee_menu_error'] =
                    'Le menu demandé est introuvable.';

                header(
                    'Location: '
                        . BASE_URL
                        . '/employee/menus'
                );
                exit;
            }
        }

        $themes = Menu::getThemes();

        $dietaryTypes =
            Menu::getDietaryTypes();

        $allDishes =
            Dish::getAllForEmployee();

        $availableDishes = array_values(
            array_filter(
                $allDishes,
                static fn(array $dish): bool =>
                (bool) $dish['is_active']
            )
        );

        $formData =
            $_SESSION['employee_menu_form_data']
            ?? null;

        $formError =
            $_SESSION['employee_menu_form_error']
            ?? null;

        unset(
            $_SESSION['employee_menu_form_data'],
            $_SESSION['employee_menu_form_error']
        );

        if (is_array($formData)) {
            $menu = array_merge(
                $menu ?? [],
                $formData
            );
        }

        $openingHours = OpeningHour::getAll();

        $pageTitle =
            $menuId
            ? 'Modifier un menu'
            : 'Créer un menu';

        $view =
            BASE_PATH
            . '/app/Views/employee/menu-form.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Crée ou modifie un menu. */
    public function saveMenu(): void
    {
        $this->requireProfessionalUser(
            '/employee/menus'
        );

        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['employee_menu_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_menu_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_menu_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/menus'
            );
            exit;
        }

        $menuId = filter_input(
            INPUT_POST,
            'menu_id',
            FILTER_VALIDATE_INT
        );

        $title = trim(
            (string) ($_POST['title'] ?? '')
        );

        $description = trim(
            (string) ($_POST['description'] ?? '')
        );

        $conditions = trim(
            (string) ($_POST['conditions'] ?? '')
        );

        $themeId = filter_input(
            INPUT_POST,
            'theme_id',
            FILTER_VALIDATE_INT
        );

        $dietaryTypeId = filter_input(
            INPUT_POST,
            'dietary_type_id',
            FILTER_VALIDATE_INT
        );

        $minimumOrderDays = filter_input(
            INPUT_POST,
            'minimum_order_days',
            FILTER_VALIDATE_INT
        );

        $minimumPeople = filter_input(
            INPUT_POST,
            'minimum_people',
            FILTER_VALIDATE_INT
        );

        $stockQuantity = filter_input(
            INPUT_POST,
            'stock_quantity',
            FILTER_VALIDATE_INT
        );

        $basePriceValue = str_replace(
            ',',
            '.',
            trim((string) ($_POST['base_price'] ?? ''))
        );

        $availableFrom = trim(
            (string) ($_POST['available_from'] ?? '')
        );

        $availableUntil = trim(
            (string) ($_POST['available_until'] ?? '')
        );

        $submittedDishIds =
            $_POST['dish_ids'] ?? [];

        if (!is_array($submittedDishIds)) {
            $submittedDishIds = [];
        }

        $dishIds = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $submittedDishIds
                    ),
                    static fn(int $id): bool =>
                    $id > 0
                )
            )
        );

        $redirectUrl =
            BASE_URL . '/employee/menu/form';

        if ($menuId) {
            $redirectUrl .= '?id=' . $menuId;
        }

        $formData = [
            'id' => $menuId ?: null,
            'theme_id' => $themeId ?: '',
            'dietary_type_id' =>
            $dietaryTypeId ?: '',
            'title' => $title,
            'description' => $description,
            'conditions' => $conditions,
            'minimum_order_days' =>
            $minimumOrderDays,
            'available_from' =>
            $availableFrom,
            'available_until' =>
            $availableUntil,
            'minimum_people' =>
            $minimumPeople,
            'base_price' =>
            $basePriceValue,
            'stock_quantity' =>
            $stockQuantity,
            'dish_ids' => $dishIds,
        ];

        if (
            mb_strlen($title) < 2
            || mb_strlen($title) > 150
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le titre doit contenir entre 2 et 150 caractères.'
            );
        }

        if ($description === '') {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'La description du menu est obligatoire.'
            );
        }

        if ($conditions === '') {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Les conditions du menu sont obligatoires.'
            );
        }

        $themes = Menu::getThemes();

        $allowedThemeIds = array_map(
            static fn(array $theme): int =>
            (int) $theme['id'],
            $themes
        );

        if (
            !$themeId
            || !in_array(
                $themeId,
                $allowedThemeIds,
                true
            )
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le thème sélectionné est invalide.'
            );
        }

        $dietaryTypes =
            Menu::getDietaryTypes();

        $allowedDietaryTypeIds = array_map(
            static fn(array $dietaryType): int =>
            (int) $dietaryType['id'],
            $dietaryTypes
        );

        if (
            !$dietaryTypeId
            || !in_array(
                $dietaryTypeId,
                $allowedDietaryTypeIds,
                true
            )
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le régime sélectionné est invalide.'
            );
        }

        if (
            $minimumOrderDays === false
            || $minimumOrderDays === null
            || $minimumOrderDays < 0
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le délai minimum de commande est invalide.'
            );
        }

        if (
            $minimumPeople === false
            || $minimumPeople === null
            || $minimumPeople < 1
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le nombre minimum de personnes doit être supérieur à zéro.'
            );
        }

        if (
            $stockQuantity === false
            || $stockQuantity === null
            || $stockQuantity < 0
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le stock disponible est invalide.'
            );
        }

        if (
            !is_numeric($basePriceValue)
            || (float) $basePriceValue <= 0
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Le prix du menu doit être supérieur à zéro.'
            );
        }

        if (
            !$this->isValidOptionalDate(
                $availableFrom
            )
            || !$this->isValidOptionalDate(
                $availableUntil
            )
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Une date de disponibilité est invalide.'
            );
        }

        if (
            $availableFrom !== ''
            && $availableUntil !== ''
            && $availableFrom > $availableUntil
        ) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'La date de fin doit être postérieure à la date de début.'
            );
        }

        $allDishes =
            Dish::getAllForEmployee();

        $activeDishes = array_values(
            array_filter(
                $allDishes,
                static fn(array $dish): bool =>
                (bool) $dish['is_active']
            )
        );

        $activeDishesById = [];

        foreach ($activeDishes as $dish) {
            $activeDishesById[(int) $dish['id']] = $dish;
        }

        $selectedDishTypes = [];

        foreach ($dishIds as $dishId) {
            if (!isset($activeDishesById[$dishId])) {
                $this->redirectMenuFormWithError(
                    $redirectUrl,
                    $formData,
                    'Un plat sélectionné est invalide ou inactif.'
                );
            }

            $selectedDishTypes[] =
                $activeDishesById[$dishId]['dish_type'];
        }

        foreach (
            ['starter', 'main_course', 'dessert']
            as $requiredDishType
        ) {
            if (!in_array(
                $requiredDishType,
                $selectedDishTypes,
                true
            )) {
                $this->redirectMenuFormWithError(
                    $redirectUrl,
                    $formData,
                    'Le menu doit contenir au moins une entrée, un plat et un dessert.'
                );
            }
        }

        $data = [
            'theme_id' => $themeId,
            'dietary_type_id' =>
            $dietaryTypeId,
            'title' => $title,
            'description' => $description,
            'conditions' => $conditions,
            'minimum_order_days' =>
            $minimumOrderDays,
            'available_from' =>
            $availableFrom !== ''
                ? $availableFrom
                : null,
            'available_until' =>
            $availableUntil !== ''
                ? $availableUntil
                : null,
            'minimum_people' =>
            $minimumPeople,
            'base_price' =>
            (float) $basePriceValue,
            'stock_quantity' =>
            $stockQuantity,
        ];

        if ($menuId) {
            if (
                Menu::findForEmployee($menuId)
                === null
            ) {
                $_SESSION['employee_menu_error'] =
                    'Le menu à modifier est introuvable.';

                header(
                    'Location: '
                        . BASE_URL
                        . '/employee/menus'
                );
                exit;
            }

            $saved = Menu::update(
                $menuId,
                $data,
                $dishIds
            );

            $successMessage =
                'Le menu a bien été modifié.';
        } else {
            $saved = Menu::create(
                $data,
                $dishIds
            );

            $successMessage =
                'Le menu a bien été créé.';
        }

        if (!$saved) {
            $this->redirectMenuFormWithError(
                $redirectUrl,
                $formData,
                'Une erreur est survenue pendant l’enregistrement du menu.'
            );
        }

        $_SESSION['employee_menu_success'] =
            $successMessage;

        header(
            'Location: '
                . BASE_URL
                . '/employee/menus'
        );
        exit;
    }

    /* Active ou désactive un menu. */
    public function toggleMenu(): void
    {
        $this->requireProfessionalUser(
            '/employee/menus'
        );

        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['employee_menu_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_menu_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_menu_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/menus'
            );
            exit;
        }

        $menuId = filter_input(
            INPUT_POST,
            'menu_id',
            FILTER_VALIDATE_INT
        );

        $requestedStatus =
            (string) ($_POST['is_active'] ?? '');

        if (
            !$menuId
            || !in_array(
                $requestedStatus,
                ['0', '1'],
                true
            )
        ) {
            $_SESSION['employee_menu_error'] =
                'La demande est invalide.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/menus'
            );
            exit;
        }

        $menu = Menu::findForEmployee($menuId);

        if ($menu === null) {
            $_SESSION['employee_menu_error'] =
                'Le menu demandé est introuvable.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/menus'
            );
            exit;
        }

        $isActive =
            $requestedStatus === '1';

        if (
            $isActive
            && !Menu::hasCompleteActiveComposition(
                $menuId
            )
        ) {
            $_SESSION['employee_menu_error'] =
                'Le menu doit contenir au moins une entrée, '
                . 'un plat et un dessert actifs avant d’être réactivé.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/menus'
            );
            exit;
        }

        $updated = Menu::setActive(
            $menuId,
            $isActive
        );

        if (!$updated) {
            $_SESSION['employee_menu_error'] =
                'Le statut du menu n’a pas pu être modifié.';
        } else {
            $_SESSION['employee_menu_success'] =
                $isActive
                ? 'Le menu a été réactivé.'
                : 'Le menu a été désactivé.';
        }

        header(
            'Location: '
                . BASE_URL
                . '/employee/menus'
        );
        exit;
    }

    /* Vérifie une date facultative au format SQL. */
    private function isValidOptionalDate(
        string $date
    ): bool {
        if ($date === '') {
            return true;
        }

        $dateObject =
            \DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $date
            );

        $errors =
            \DateTimeImmutable::getLastErrors();

        return
            $dateObject !== false
            && (
                $errors === false
                || (
                    $errors['warning_count'] === 0
                    && $errors['error_count'] === 0
                )
            );
    }

    /* Conserve le formulaire et retourne l'erreur. */
    private function redirectMenuFormWithError(
        string $redirectUrl,
        array $formData,
        string $message
    ): never {
        $_SESSION['employee_menu_form_data'] =
            $formData;

        $_SESSION['employee_menu_form_error'] =
            $message;

        header('Location: ' . $redirectUrl);
        exit;
    }

    /* -------------------------------------------------- */
    /* gestion des plats */
    /* -------------------------------------------------- */

    /* Affiche la liste des plats. */
    public function dishes(): void
    {
        $this->requireProfessionalUser(
            '/employee/dishes'
        );

        if (empty($_SESSION['employee_dish_csrf_token'])) {
            $_SESSION['employee_dish_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['employee_dish_csrf_token'];

        $dishes = Dish::getAllForEmployee();

        $success =
            $_SESSION['employee_dish_success'] ?? null;

        $error =
            $_SESSION['employee_dish_error'] ?? null;

        unset(
            $_SESSION['employee_dish_success'],
            $_SESSION['employee_dish_error']
        );

        $openingHours = OpeningHour::getAll();
        $pageTitle = 'Gestion des plats';

        $view =
            BASE_PATH . '/app/Views/employee/dishes.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Affiche le formulaire de création ou modification. */
    public function dishForm(): void
    {
        $this->requireProfessionalUser(
            '/employee/dishes'
        );

        if (empty($_SESSION['employee_dish_csrf_token'])) {
            $_SESSION['employee_dish_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['employee_dish_csrf_token'];

        $dishId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $dish = null;

        if ($dishId) {
            $dish = Dish::findForEmployee($dishId);

            if ($dish === null) {
                $_SESSION['employee_dish_error'] =
                    'Le plat demandé est introuvable.';

                header(
                    'Location: '
                        . BASE_URL
                        . '/employee/dishes'
                );
                exit;
            }
        }

        $allergens = Dish::getAllAllergens();

        $formData =
            $_SESSION['employee_dish_form_data'] ?? null;

        $formError =
            $_SESSION['employee_dish_form_error'] ?? null;

        unset(
            $_SESSION['employee_dish_form_data'],
            $_SESSION['employee_dish_form_error']
        );

        if (is_array($formData)) {
            $dish = array_merge(
                $dish ?? [],
                $formData
            );
        }

        $openingHours = OpeningHour::getAll();

        $pageTitle =
            $dishId
            ? 'Modifier un plat'
            : 'Créer un plat';

        $view =
            BASE_PATH
            . '/app/Views/employee/dish-form.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Enregistre un nouveau plat ou une modification. */
    public function saveDish(): void
    {
        $this->requireProfessionalUser(
            '/employee/dishes'
        );

        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['employee_dish_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_dish_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_dish_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/dishes'
            );
            exit;
        }

        $dishId = filter_input(
            INPUT_POST,
            'dish_id',
            FILTER_VALIDATE_INT
        );

        $name = trim(
            (string) ($_POST['name'] ?? '')
        );

        $description = trim(
            (string) ($_POST['description'] ?? '')
        );

        $dishType = trim(
            (string) ($_POST['dish_type'] ?? '')
        );

        $submittedAllergens =
            $_POST['allergen_ids'] ?? [];

        if (!is_array($submittedAllergens)) {
            $submittedAllergens = [];
        }

        $allergenIds = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $submittedAllergens
                    ),
                    static fn(int $id): bool => $id > 0
                )
            )
        );

        $redirectUrl =
            BASE_URL . '/employee/dish/form';

        if ($dishId) {
            $redirectUrl .= '?id=' . $dishId;
        }

        $formData = [
            'id' => $dishId ?: null,
            'name' => $name,
            'description' => $description,
            'dish_type' => $dishType,
            'allergen_ids' => $allergenIds,
        ];

        if (
            mb_strlen($name) < 2
            || mb_strlen($name) > 150
        ) {
            $this->redirectDishFormWithError(
                $redirectUrl,
                $formData,
                'Le nom doit contenir entre 2 et 150 caractères.'
            );
        }

        if (
            $description !== ''
            && mb_strlen($description) > 3000
        ) {
            $this->redirectDishFormWithError(
                $redirectUrl,
                $formData,
                'La description ne doit pas dépasser 3 000 caractères.'
            );
        }

        $allowedDishTypes = [
            'starter',
            'main_course',
            'dessert',
        ];

        if (!in_array(
            $dishType,
            $allowedDishTypes,
            true
        )) {
            $this->redirectDishFormWithError(
                $redirectUrl,
                $formData,
                'Le type de plat sélectionné est invalide.'
            );
        }

        if (Dish::nameExists(
            $name,
            $dishId ?: null
        )) {
            $this->redirectDishFormWithError(
                $redirectUrl,
                $formData,
                'Un plat porte déjà ce nom.'
            );
        }

        $availableAllergens =
            Dish::getAllAllergens();

        $allowedAllergenIds = array_map(
            static fn(array $allergen): int =>
            (int) $allergen['id'],
            $availableAllergens
        );

        foreach ($allergenIds as $allergenId) {
            if (!in_array(
                $allergenId,
                $allowedAllergenIds,
                true
            )) {
                $this->redirectDishFormWithError(
                    $redirectUrl,
                    $formData,
                    'Un allergène sélectionné est invalide.'
                );
            }
        }

        $photo = null;
        $photoMimeType = null;

        $uploadedPhoto =
            $_FILES['photo'] ?? null;

        if (
            is_array($uploadedPhoto)
            && (int) $uploadedPhoto['error']
            !== UPLOAD_ERR_NO_FILE
        ) {
            if (
                (int) $uploadedPhoto['error']
                !== UPLOAD_ERR_OK
            ) {
                $this->redirectDishFormWithError(
                    $redirectUrl,
                    $formData,
                    'La photo n’a pas pu être envoyée.'
                );
            }

            if ((int) $uploadedPhoto['size'] > 1024 * 1024) {
                $this->redirectDishFormWithError(
                    $redirectUrl,
                    $formData,
                    'La photo ne doit pas dépasser 1 Mo.'
                );
            }

            $temporaryPath =
                (string) $uploadedPhoto['tmp_name'];

            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);

            $detectedMimeType =
                $fileInfo->file($temporaryPath);

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (
                !is_string($detectedMimeType)
                || !in_array(
                    $detectedMimeType,
                    $allowedMimeTypes,
                    true
                )
            ) {
                $this->redirectDishFormWithError(
                    $redirectUrl,
                    $formData,
                    'La photo doit être au format JPG, PNG ou WebP.'
                );
            }

            $photoContent =
                file_get_contents($temporaryPath);

            if ($photoContent === false) {
                $this->redirectDishFormWithError(
                    $redirectUrl,
                    $formData,
                    'La photo est illisible.'
                );
            }

            $photo = $photoContent;
            $photoMimeType = $detectedMimeType;
        }

        $data = [
            'name' => $name,
            'description' =>
            $description !== ''
                ? $description
                : null,
            'dish_type' => $dishType,
            'photo' => $photo,
            'photo_mime_type' => $photoMimeType,
        ];

        if ($dishId) {
            if (Dish::findForEmployee($dishId) === null) {
                $_SESSION['employee_dish_error'] =
                    'Le plat à modifier est introuvable.';

                header(
                    'Location: '
                        . BASE_URL
                        . '/employee/dishes'
                );
                exit;
            }

            $saved = Dish::update(
                $dishId,
                $data,
                $allergenIds
            );

            $successMessage =
                'Le plat a bien été modifié.';
        } else {
            $saved = Dish::create(
                $data,
                $allergenIds
            );

            $successMessage =
                'Le plat a bien été créé.';
        }

        if (!$saved) {
            $this->redirectDishFormWithError(
                $redirectUrl,
                $formData,
                'Une erreur est survenue pendant l’enregistrement.'
            );
        }

        $_SESSION['employee_dish_success'] =
            $successMessage;

        header(
            'Location: '
                . BASE_URL
                . '/employee/dishes'
        );
        exit;
    }

    /* Active ou désactive un plat. */
    public function toggleDish(): void
    {
        $this->requireProfessionalUser(
            '/employee/dishes'
        );

        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['employee_dish_csrf_token'])
            || !hash_equals(
                $_SESSION['employee_dish_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['employee_dish_error'] =
                'Le formulaire a expiré. Veuillez recommencer.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/dishes'
            );
            exit;
        }

        $dishId = filter_input(
            INPUT_POST,
            'dish_id',
            FILTER_VALIDATE_INT
        );

        $requestedStatus =
            (string) ($_POST['is_active'] ?? '');

        if (
            !$dishId
            || !in_array(
                $requestedStatus,
                ['0', '1'],
                true
            )
        ) {
            $_SESSION['employee_dish_error'] =
                'La demande est invalide.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/dishes'
            );
            exit;
        }

        $dish = Dish::findForEmployee($dishId);

        if ($dish === null) {
            $_SESSION['employee_dish_error'] =
                'Le plat demandé est introuvable.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/dishes'
            );
            exit;
        }

        $isActive = $requestedStatus === '1';

        if (
            !$isActive
            && Dish::countActiveMenus($dishId) > 0
        ) {
            $_SESSION['employee_dish_error'] =
                'Ce plat appartient encore à un menu actif. '
                . 'Retirez-le d’abord des menus concernés.';

            header(
                'Location: '
                    . BASE_URL
                    . '/employee/dishes'
            );
            exit;
        }

        $updated = Dish::setActive(
            $dishId,
            $isActive
        );

        if (!$updated) {
            $_SESSION['employee_dish_error'] =
                'Le statut du plat n’a pas pu être modifié.';
        } else {
            $_SESSION['employee_dish_success'] =
                $isActive
                ? 'Le plat a été réactivé.'
                : 'Le plat a été désactivé.';
        }

        header(
            'Location: '
                . BASE_URL
                . '/employee/dishes'
        );
        exit;
    }

    /* Vérifie l'accès professionnel. */
    private function requireProfessionalUser(
        string $redirectPath
    ): array {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . $redirectPath;

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = User::findById(
            (int) $_SESSION['user']['id']
        );

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

            echo 'Accès refusé.';
            exit;
        }

        return $user;
    }

    /* Conserve le formulaire et retourne l'erreur. */
    private function redirectDishFormWithError(
        string $redirectUrl,
        array $formData,
        string $message
    ): never {
        $_SESSION['employee_dish_form_data'] =
            $formData;

        $_SESSION['employee_dish_form_error'] =
            $message;

        header('Location: ' . $redirectUrl);
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
