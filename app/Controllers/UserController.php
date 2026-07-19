<?php

namespace App\Controllers;

use App\Models\Menu;
use App\Models\OpeningHour;
use App\Models\Order;
use App\Models\User;
use App\Services\DeliveryService;
use App\Services\OrderService;


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

        /* Récupère le message envoyé après une modification. */
        $success = $_SESSION['user_success'] ?? null;

        unset($_SESSION['user_success']);

        $pageTitle = 'Mon compte';

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/user/dashboard.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
    /* -------------------------------------------------- */
    /* détail d'une commande */
    /* -------------------------------------------------- */

    /* Affiche une commande appartenant à l'utilisateur. */
    public function orderDetail(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/user/dashboard';

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

        /* Limite cette page aux comptes utilisateurs. */
        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas accéder à cet espace.</p>';

            return;
        }

        /* Vérifie la présence d'un identifiant valide. */
        $orderId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if ($orderId === false || $orderId === null || $orderId < 1) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable ou inaccessible.';

            $openingHours = OpeningHour::getAll();

            $view = BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Récupère uniquement une commande appartenant au client. */
        $order = Order::findByIdAndUserId(
            (int) $orderId,
            $userId
        );

        if ($order === null) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable ou inaccessible.';

            $openingHours = OpeningHour::getAll();

            $view = BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Récupère les changements de statut de la commande. */
        $statusHistory = Order::findStatusHistoryByOrderAndUser(
            (int) $orderId,
            $userId
        );
        /* Crée le jeton protégeant le formulaire d'annulation. */
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        $csrfToken = $_SESSION['csrf_token'];

        /* Récupère les éventuels messages de confirmation ou d'erreur. */
        $success = $_SESSION['user_success'] ?? null;
        $error = $_SESSION['user_error'] ?? null;

        unset(
            $_SESSION['user_success'],
            $_SESSION['user_error']
        );

        $pageTitle = 'Détail de la commande';

        /* Récupère les horaires affichés dans le footer. */
        $openingHours = OpeningHour::getAll();

        /* Définit la vue affichée dans le layout principal. */
        $view = BASE_PATH . '/app/Views/user/order-detail.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* -------------------------------------------------- */
    /* modification d'une commande */
    /* -------------------------------------------------- */

    /* Affiche le formulaire d'une commande encore en attente. */
    public function editOrder(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/user/dashboard';

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

        /* Limite cette page aux comptes utilisateurs. */
        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas accéder à cet espace.</p>';

            return;
        }

        /* Vérifie la présence d'un identifiant valide. */
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
                'La commande demandée est introuvable ou inaccessible.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Récupère uniquement une commande appartenant au client. */
        $order = Order::findByIdAndUserId(
            (int) $orderId,
            $userId
        );

        if ($order === null) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable ou inaccessible.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* La modification est autorisée uniquement en attente. */
        if ($order['status_name'] !== 'En attente') {
            $_SESSION['user_error'] =
                'Cette commande ne peut plus être modifiée, '
                . 'car elle a déjà été prise en charge.';

            header(
                'Location: '
                    . BASE_URL
                    . '/user/order/detail?id='
                    . (int) $orderId
            );

            exit;
        }

        /* Récupère le menu initial de la commande. */
        $menu = Menu::findByIdWithDishesAndAllergens(
            (int) $order['menu_id']
        );

        if ($menu === null) {
            http_response_code(404);

            $pageTitle = 'Menu introuvable';
            $errorTitle = 'Menu introuvable';
            $errorMessage =
                'Le menu associé à cette commande est introuvable.';

            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Calcule la première date encore autorisée. */
        $today = new \DateTimeImmutable('today');

        $minimumEventDate = $today
            ->modify(
                '+'
                    . (int) $menu['minimum_order_days']
                    . ' days'
            )
            ->format('Y-m-d');

        $maximumEventDate = null;
        $availabilityMessage = null;

        /* Applique la période des menus saisonniers. */
        if (
            !empty($menu['available_from'])
            && !empty($menu['available_until'])
        ) {
            $availableFrom = $menu['available_from'];
            $availableUntil = $menu['available_until'];

            if ($minimumEventDate < $availableFrom) {
                $minimumEventDate = $availableFrom;
            }

            $maximumEventDate = $availableUntil;

            if ($minimumEventDate > $maximumEventDate) {
                $availabilityMessage =
                    'Ce menu n’est actuellement plus disponible '
                    . 'à la modification.';
            } else {
                $availabilityMessage =
                    'Ce menu est disponible pour une prestation '
                    . 'comprise entre le '
                    . date(
                        'd/m/Y',
                        strtotime($minimumEventDate)
                    )
                    . ' et le '
                    . date(
                        'd/m/Y',
                        strtotime($maximumEventDate)
                    )
                    . '.';
            }
        }

        /* Crée un jeton dédié à la modification. */
        if (empty($_SESSION['order_edit_csrf_token'])) {
            $_SESSION['order_edit_csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        /*
         * Le stock affiché inclut les portions déjà réservées
         * dans cette commande.
         */
        $availableStock =
            (int) $menu['stock_quantity']
            + (int) $order['people_count'];

        $formData = [
            'first_name' =>
            $order['customer_first_name'],
            'last_name' =>
            $order['customer_last_name'],
            'email' =>
            $order['customer_email'],
            'phone' =>
            $order['customer_phone'],
            'delivery_address' =>
            $order['delivery_address'],
            'delivery_postal_code' =>
            $order['delivery_postal_code'],
            'delivery_city' =>
            $order['delivery_city'],
            'event_date' =>
            $order['event_date'],
            'delivery_time' =>
            substr($order['delivery_time'], 0, 5),
            'people_count' =>
            (int) $order['people_count'],
        ];

        $deliveryTimes = OrderService::getDeliveryTimes();

        $errors = [];
        $pageTitle = 'Modifier ma commande';

        $openingHours = OpeningHour::getAll();

        $view =
            BASE_PATH . '/app/Views/user/order-edit.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Vérifie et enregistre la modification d'une commande. */
    public function updateOrder(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo 'Méthode non autorisée.';

            return;
        }

        $userId = (int) $_SESSION['user']['id'];
        $user = User::findById($userId);

        if ($user === null) {
            unset($_SESSION['user']);

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas accéder à cet espace.</p>';

            return;
        }

        $orderId = filter_input(
            INPUT_POST,
            'order_id',
            FILTER_VALIDATE_INT
        );

        if (!$orderId || $orderId < 1) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable ou inaccessible.';

            $openingHours = OpeningHour::getAll();
            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Vérifie le jeton du formulaire. */
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['order_edit_csrf_token'])
            || !hash_equals(
                $_SESSION['order_edit_csrf_token'],
                $csrfToken
            )
        ) {
            http_response_code(403);

            echo 'Le formulaire a expiré. Veuillez recommencer.';

            return;
        }

        /*
         * Récupère la commande depuis la base.
         * Le menu provient uniquement de cette commande.
         */
        $order = Order::findByIdAndUserId(
            (int) $orderId,
            $userId
        );

        if ($order === null) {
            http_response_code(404);

            $pageTitle = 'Commande introuvable';
            $errorTitle = 'Commande introuvable';
            $errorMessage =
                'La commande demandée est introuvable ou inaccessible.';

            $openingHours = OpeningHour::getAll();
            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        if ($order['status_name'] !== 'En attente') {
            $_SESSION['user_error'] =
                'Cette commande ne peut plus être modifiée, '
                . 'car elle a déjà été prise en charge.';

            header(
                'Location: '
                    . BASE_URL
                    . '/user/order/detail?id='
                    . (int) $orderId
            );

            exit;
        }

        $menu = Menu::findByIdWithDishesAndAllergens(
            (int) $order['menu_id']
        );

        if ($menu === null) {
            http_response_code(404);

            $pageTitle = 'Menu introuvable';
            $errorTitle = 'Menu introuvable';
            $errorMessage =
                'Le menu associé à cette commande est introuvable.';

            $openingHours = OpeningHour::getAll();
            $view =
                BASE_PATH . '/app/Views/user/order-not-found.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        $errors = [];

        $firstName = trim(
            (string) ($_POST['first_name'] ?? '')
        );

        $lastName = trim(
            (string) ($_POST['last_name'] ?? '')
        );

        $email = trim(
            (string) ($_POST['email'] ?? '')
        );

        $phone = trim(
            (string) ($_POST['phone'] ?? '')
        );

        $deliveryAddress = trim(
            (string) ($_POST['delivery_address'] ?? '')
        );

        $deliveryPostalCode = trim(
            (string) ($_POST['delivery_postal_code'] ?? '')
        );

        $deliveryCity = trim(
            (string) ($_POST['delivery_city'] ?? '')
        );

        $deliveryLatitude = filter_input(
            INPUT_POST,
            'delivery_latitude',
            FILTER_VALIDATE_FLOAT
        );

        $deliveryLongitude = filter_input(
            INPUT_POST,
            'delivery_longitude',
            FILTER_VALIDATE_FLOAT
        );

        $eventDate = trim(
            (string) ($_POST['event_date'] ?? '')
        );

        $deliveryTime = trim(
            (string) ($_POST['delivery_time'] ?? '')
        );

        $peopleCount = filter_input(
            INPUT_POST,
            'people_count',
            FILTER_VALIDATE_INT
        );

        /* Vérifie les coordonnées du client. */
        if (
            $firstName === ''
            || $lastName === ''
            || $email === ''
            || $phone === ''
        ) {
            $errors[] =
                'Les coordonnées du client sont obligatoires.';
        }

        if (
            $email !== ''
            && filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            $errors[] =
                'L’adresse mail indiquée est invalide.';
        }

        /* Vérifie l'adresse sélectionnée. */
        if (
            $deliveryAddress === ''
            || $deliveryPostalCode === ''
            || $deliveryCity === ''
        ) {
            $errors[] =
                'L’adresse de livraison est incomplète.';
        }

        if (
            $deliveryLatitude === false
            || $deliveryLongitude === false
            || $deliveryLatitude === null
            || $deliveryLongitude === null
        ) {
            $errors[] =
                'Veuillez rechercher et sélectionner une adresse proposée.';
        }

        /* Vérifie le nombre minimum et le stock disponible. */
        $availableStock =
            (int) $menu['stock_quantity']
            + (int) $order['people_count'];

        if (
            $peopleCount === false
            || $peopleCount === null
            || $peopleCount < (int) $menu['minimum_people']
        ) {
            $errors[] =
                'Le nombre minimum de personnes pour ce menu est de '
                . (int) $menu['minimum_people']
                . '.';
        } elseif ($peopleCount > $availableStock) {
            $errors[] =
                'Le stock disponible permet actuellement de servir '
                . $availableStock
                . ' personne(s) maximum pour cette commande.';
        }

        /* Réapplique les règles de date et de saison. */
        $dateValidation =
            OrderService::validateEventDate(
                $eventDate,
                $menu
            );

        $errors = array_merge(
            $errors,
            $dateValidation['errors']
        );

        if (
            !OrderService::isValidDeliveryTime(
                $deliveryTime
            )
        ) {
            $errors[] =
                'Le créneau de livraison sélectionné est invalide.';
        }

        $formData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'delivery_address' => $deliveryAddress,
            'delivery_postal_code' =>
            $deliveryPostalCode,
            'delivery_city' => $deliveryCity,
            'event_date' => $eventDate,
            'delivery_time' => $deliveryTime,
            'people_count' =>
            $peopleCount === false
                || $peopleCount === null
                ? ''
                : (int) $peopleCount,
        ];

        /*
         * Prépare les données nécessaires au réaffichage
         * en cas d'erreur.
         */
        $today = new \DateTimeImmutable('today');

        $minimumEventDate = $today
            ->modify(
                '+'
                    . (int) $menu['minimum_order_days']
                    . ' days'
            )
            ->format('Y-m-d');

        $maximumEventDate = null;
        $availabilityMessage = null;

        if (
            !empty($menu['available_from'])
            && !empty($menu['available_until'])
        ) {
            if ($minimumEventDate < $menu['available_from']) {
                $minimumEventDate = $menu['available_from'];
            }

            $maximumEventDate = $menu['available_until'];

            $availabilityMessage =
                'Ce menu est disponible pour une prestation '
                . 'comprise entre le '
                . date('d/m/Y', strtotime($minimumEventDate))
                . ' et le '
                . date('d/m/Y', strtotime($maximumEventDate))
                . '.';
        }

        $deliveryTimes = OrderService::getDeliveryTimes();

        if (!empty($errors)) {
            http_response_code(422);

            $pageTitle = 'Modifier ma commande';
            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-edit.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Recalcule la distance et la livraison côté serveur. */
        $distance = DeliveryService::calculateDistance(
            44.8378,
            -0.5792,
            (float) $deliveryLatitude,
            (float) $deliveryLongitude
        );

        if ($distance === null) {
            $errors[] =
                'La distance de livraison n’a pas pu être recalculée.';

            http_response_code(502);

            $pageTitle = 'Modifier ma commande';
            $openingHours = OpeningHour::getAll();

            $view =
                BASE_PATH . '/app/Views/user/order-edit.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        $deliveryFees =
            DeliveryService::calculateDeliveryFees(
                $distance,
                $deliveryCity
            );

        /* Recalcule tous les prix côté serveur. */
        $priceCalculation =
            OrderService::calculateMenuPrice(
                $menu,
                (int) $peopleCount
            );

        $menuPrice =
            (float) $priceCalculation['menu_price'];

        $deliveryPrice = round(
            (float) $deliveryFees['delivery_fee'],
            2
        );

        $totalPrice =
            OrderService::calculateTotalPrice(
                $menuPrice,
                $deliveryPrice
            );

        $result = Order::updateByUser(
            (int) $orderId,
            $userId,
            [
                'customer_first_name' => $firstName,
                'customer_last_name' => $lastName,
                'customer_email' => $email,
                'customer_phone' => $phone,
                'delivery_address' => $deliveryAddress,
                'delivery_postal_code' =>
                $deliveryPostalCode,
                'delivery_city' => $deliveryCity,
                'event_date' => $eventDate,
                'delivery_time' => $deliveryTime,
                'people_count' => (int) $peopleCount,
                'menu_price' => $menuPrice,
                'delivery_price' => $deliveryPrice,
                'total_price' => $totalPrice,
            ]
        );

        if ($result === 'updated') {
            unset($_SESSION['order_edit_csrf_token']);

            $_SESSION['user_success'] =
                'Votre commande a bien été modifiée.';

            header(
                'Location: '
                    . BASE_URL
                    . '/user/order/detail?id='
                    . (int) $orderId
            );

            exit;
        }

        if ($result === 'insufficient_stock') {
            $_SESSION['user_error'] =
                'Le stock disponible a changé. '
                . 'Veuillez vérifier le nombre de personnes.';
        } elseif ($result === 'not_allowed') {
            $_SESSION['user_error'] =
                'Cette commande ne peut plus être modifiée, '
                . 'car elle a déjà été prise en charge.';
        } elseif ($result === 'not_found') {
            $_SESSION['user_error'] =
                'La commande ou son menu est introuvable.';
        } else {
            $_SESSION['user_error'] =
                'Une erreur est survenue pendant la modification.';
        }

        header(
            'Location: '
                . BASE_URL
                . '/user/order/detail?id='
                . (int) $orderId
        );

        exit;
    }

    /* -------------------------------------------------- */
    /* modification du profil */
    /* -------------------------------------------------- */

    /* Affiche le formulaire des informations personnelles. */
    public function editProfile(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . '/user/profile/edit';

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

        /* Limite cette page aux comptes utilisateurs. */
        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas accéder à cet espace.</p>';

            return;
        }

        $errors = [];
        $formData = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'phone' => $user['phone'],
            'email' => $user['email'],
            'address' => $user['address'],
            'postal_code' => $user['postal_code'],
            'city' => $user['city'],
        ];

        $pageTitle = 'Modifier mes informations';
        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/user/profile-edit.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Vérifie et enregistre les informations personnelles. */
    public function updateProfile(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
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

        /* Limite cette action aux comptes utilisateurs. */
        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas effectuer cette action.</p>';

            return;
        }

        $errors = [];

        $formData = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => strtolower(
                trim($_POST['email'] ?? '')
            ),
            'address' => trim($_POST['address'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
        ];

        /* Vérifie que tous les champs sont renseignés. */
        foreach ($formData as $value) {
            if ($value === '') {
                $errors[] =
                    'Tous les champs sont obligatoires.';

                break;
            }
        }

        /* Vérifie le format de l'adresse mail. */
        if (
            $formData['email'] !== ''
            && !filter_var(
                $formData['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] = 'L’adresse mail n’est pas valide.';
        }

        /* Empêche l'utilisation de l'email d'un autre compte. */
        if (
            $formData['email'] !== ''
            && User::emailExistsForAnotherUser(
                $formData['email'],
                $userId
            )
        ) {
            $errors[] =
                'Cette adresse mail est déjà utilisée.';
        }

        /* Vérifie le format du code postal français. */
        if (
            $formData['postal_code'] !== ''
            && !preg_match(
                '/^[0-9]{5}$/',
                $formData['postal_code']
            )
        ) {
            $errors[] =
                'Le code postal doit contenir 5 chiffres.';
        }

        /* Réaffiche le formulaire lorsqu'une erreur existe. */
        if (!empty($errors)) {
            $pageTitle = 'Modifier mes informations';
            $openingHours = OpeningHour::getAll();

            $view = BASE_PATH
                . '/app/Views/user/profile-edit.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        $updated = User::updateProfile(
            $userId,
            $formData
        );

        if (!$updated) {
            $errors[] =
                'Une erreur est survenue pendant la modification.';

            $pageTitle = 'Modifier mes informations';
            $openingHours = OpeningHour::getAll();

            $view = BASE_PATH
                . '/app/Views/user/profile-edit.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Actualise les informations conservées en session. */
        $_SESSION['user']['first_name'] =
            $formData['first_name'];

        $_SESSION['user']['last_name'] =
            $formData['last_name'];

        $_SESSION['user']['email'] =
            $formData['email'];

        $_SESSION['user_success'] =
            'Vos informations personnelles ont bien été modifiées.';

        header('Location: ' . BASE_URL . '/user/dashboard');
        exit;
    }
    /* -------------------------------------------------- */
    /* annulation d'une commande */
    /* -------------------------------------------------- */

    /* Annule une commande encore en attente. */
    public function cancelOrder(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user']['id'])) {
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

        /* Limite cette action aux comptes utilisateurs. */
        if ($user['role'] !== 'user') {
            http_response_code(403);

            echo '<h1>Erreur 403</h1>';
            echo '<p>Vous ne pouvez pas effectuer cette action.</p>';

            return;
        }

        $orderId = filter_input(
            INPUT_POST,
            'order_id',
            FILTER_VALIDATE_INT
        );

        /* Prépare le retour vers le détail si possible. */
        $redirectUrl = BASE_URL . '/user/dashboard';

        if (
            $orderId !== false
            && $orderId !== null
            && $orderId > 0
        ) {
            $redirectUrl = BASE_URL
                . '/user/order/detail?id='
                . (int) $orderId;
        }

        /* Vérifie le jeton du formulaire. */
        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (
            $submittedToken === ''
            || $sessionToken === ''
            || !hash_equals(
                $sessionToken,
                $submittedToken
            )
        ) {
            $_SESSION['user_error'] =
                'La demande a expiré. Veuillez réessayer.';

            header('Location: ' . $redirectUrl);
            exit;
        }

        /* Vérifie la présence d'une commande valide. */
        if (
            $orderId === false
            || $orderId === null
            || $orderId < 1
        ) {
            $_SESSION['user_error'] =
                'La commande demandée est introuvable.';

            header('Location: ' . BASE_URL . '/user/dashboard');
            exit;
        }

        $result = Order::cancelByUser(
            (int) $orderId,
            $userId
        );

        if ($result === 'cancelled') {
            /*
             * Renouvelle le jeton après l'action pour empêcher
             * une nouvelle soumission du même formulaire.
             */
            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );

            $_SESSION['user_success'] =
                'Votre commande a bien été annulée. '
                . 'Le stock du menu a été rétabli.';

            header('Location: ' . BASE_URL . '/user/dashboard');
            exit;
        }

        if ($result === 'not_allowed') {
            $_SESSION['user_error'] =
                'Cette commande ne peut plus être annulée, '
                . 'car elle a déjà été prise en charge.';

            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($result === 'not_found') {
            $_SESSION['user_error'] =
                'La commande demandée est introuvable '
                . 'ou ne vous appartient pas.';

            header('Location: ' . BASE_URL . '/user/dashboard');
            exit;
        }

        $_SESSION['user_error'] =
            'Une erreur est survenue pendant l’annulation. '
            . 'Veuillez réessayer.';

        header('Location: ' . $redirectUrl);
        exit;
    }
}
