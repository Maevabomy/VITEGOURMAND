<?php

namespace App\Controllers;

use App\Models\Menu;
use App\Models\OpeningHour;
use App\Models\Order;
use App\Models\User;
use App\Services\DeliveryService;
use App\Services\MailService;

class OrderController
{
    /* -------------------------------------------------- */
    /* affichage de la commande */
    /* -------------------------------------------------- */

    /* Prépare le formulaire de commande. */
    public function create(): void
    {
        /* Redirige les visiteurs vers la connexion. */
        if (empty($_SESSION['user'])) {
            $menuId = filter_input(
                INPUT_GET,
                'menu_id',
                FILTER_VALIDATE_INT
            );

            $redirect = BASE_URL . '/order/create';

            if ($menuId && $menuId > 0) {
                $redirect .= '?menu_id=' . $menuId;
            }

            $_SESSION['redirect_after_login'] = $redirect;

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $menuId = filter_input(
            INPUT_GET,
            'menu_id',
            FILTER_VALIDATE_INT
        );

        /* Vérifie l'identifiant du menu. */
        if (!$menuId || $menuId < 1) {
            http_response_code(404);

            echo '<h1>Erreur 404</h1>';
            echo '<p>Le menu demandé est introuvable.</p>';

            return;
        }

        $menu = Menu::findByIdWithDishesAndAllergens($menuId);

        /* Vérifie que le menu existe et reste disponible. */
        if ($menu === null || $menu['stock_quantity'] < 1) {
            http_response_code(404);

            echo '<h1>Menu indisponible</h1>';
            echo '<p>Ce menu ne peut plus être commandé.</p>';

            return;
        }

        /* Calcule la première date autorisée selon le délai du menu. */
        $today = new \DateTimeImmutable('today');

        $minimumEventDate = $today
            ->modify('+' . (int) $menu['minimum_order_days'] . ' days')
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
                    'Ce menu n’est actuellement plus disponible à la commande.';
            } else {
                $availabilityMessage =
                    'Ce menu peut être commandé pour une prestation comprise entre le '
                    . date('d/m/Y', strtotime($minimumEventDate))
                    . ' et le '
                    . date('d/m/Y', strtotime($maximumEventDate))
                    . '.';
            }
        }

        $user = User::findById(
            (int) $_SESSION['user']['id']
        );

        /* Déconnecte la session si le compte n'existe plus. */
        if ($user === null) {
            $_SESSION = [];

            session_destroy();

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (empty($_SESSION['order_csrf_token'])) {
            $_SESSION['order_csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        $pageTitle = 'Commander le menu ' . $menu['title'];
        $errors = [];

        if ($availabilityMessage !== null && $minimumEventDate > $maximumEventDate) {
            $errors[] = $availabilityMessage;
        }

        $formData = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'delivery_address' => $user['address'],
            'delivery_postal_code' => $user['postal_code'],
            'delivery_city' => $user['city'],
            'event_date' => '',
            'delivery_time' => '',
            'people_count' => $menu['minimum_people'],
        ];

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/orders/create.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
    /* -------------------------------------------------- */
    /* recherche d'adresse */
    /* -------------------------------------------------- */

    /* Retourne les propositions d'adresses au format JSON. */
    public function searchAddress(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (empty($_SESSION['user'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Vous devez être connecté.',
            ]);

            return;
        }

        $query = trim($_GET['query'] ?? '');

        if (mb_strlen($query) < 5) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' =>
                'Saisissez au moins 5 caractères.',
            ]);

            return;
        }

        $addresses = DeliveryService::searchAddresses($query);

        echo json_encode([
            'success' => true,
            'addresses' => $addresses,
        ], JSON_UNESCAPED_UNICODE);
    }
    /* -------------------------------------------------- */
    /* calcul de distance */
    /* -------------------------------------------------- */

    /* Retourne la distance routière au format JSON. */
    public function calculateDistance(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (empty($_SESSION['user'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Vous devez être connecté.',
            ]);

            return;
        }

        $latitude = filter_input(
            INPUT_GET,
            'latitude',
            FILTER_VALIDATE_FLOAT
        );

        $longitude = filter_input(
            INPUT_GET,
            'longitude',
            FILTER_VALIDATE_FLOAT
        );

        $city = trim((string) filter_input(
            INPUT_GET,
            'city',
            FILTER_UNSAFE_RAW
        ));

        if (
            $latitude === false || $longitude === false || $city === ''
        ) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' =>
                'Les coordonnées ou la ville de livraison sont invalides.',
            ]);

            return;
        }

        /* Coordonnées du traiteur à Bordeaux. */
        $startLatitude = 44.8378;
        $startLongitude = -0.5792;

        $distance = DeliveryService::calculateDistance(
            $startLatitude,
            $startLongitude,
            (float) $latitude,
            (float) $longitude
        );

        if ($distance === null) {
            http_response_code(502);

            echo json_encode([
                'success' => false,
                'message' =>
                'La distance de livraison n’a pas pu être calculée.',
            ]);

            return;
        }
        $deliveryFees = DeliveryService::calculateDeliveryFees(
            $distance,
            $city
        );

        /* Évite l’affichage des décimales flottantes parasites. */
        ini_set('serialize_precision', '-1');

        echo json_encode([
            'success' => true,
            'distance' => round($distance, 2),
            'is_bordeaux' => $deliveryFees['is_bordeaux'],
            'fixed_fee' => $deliveryFees['fixed_fee'],
            'distance_fee' => $deliveryFees['distance_fee'],
            'delivery_fee' => $deliveryFees['delivery_fee'],
        ], JSON_UNESCAPED_UNICODE);
    }

    /* -------------------------------------------------- */
    /* validation de la commande */
    /* -------------------------------------------------- */

    /* Reçoit le formulaire sans enregistrer la commande. */
    public function store(): void
    {
        if (empty($_SESSION['user'])) {
            header(
                'Location: '
                    . BASE_URL
                    . '/login'
            );

            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo 'Méthode non autorisée.';

            return;
        }

        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['order_csrf_token'])
            || !hash_equals(
                $_SESSION['order_csrf_token'],
                $csrfToken
            )
        ) {
            http_response_code(403);

            echo 'Le formulaire a expiré. Veuillez recommencer.';

            return;
        }

        $errors = [];

        $menuId = filter_input(
            INPUT_POST,
            'menu_id',
            FILTER_VALIDATE_INT
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

        /* Vérifie le menu depuis la base de données. */
        if (!$menuId || $menuId < 1) {
            $errors[] = 'Le menu sélectionné est invalide.';
        }

        $menu = null;

        if (empty($errors)) {
            $menu = Menu::findByIdWithDishesAndAllergens(
                (int) $menuId
            );

            if (
                $menu === null
                || (int) $menu['stock_quantity'] < 1
            ) {
                $errors[] =
                    'Le menu sélectionné n’est plus disponible.';
            }
        }

        /* Vérifie qu’une adresse OpenStreetMap a été sélectionnée. */
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

        /* Vérifie le nombre de personnes. */
        if (
            $menu !== null
            && (
                $peopleCount === false
                || $peopleCount === null
                || $peopleCount < (int) $menu['minimum_people']
            )
        ) {
            $errors[] =
                'Le nombre minimum de personnes pour ce menu est de '
                . (int) $menu['minimum_people']
                . '.';
        }

        /* Vérifie que le stock couvre le nombre de personnes. */
        if (
            $menu !== null
            && $peopleCount !== false
            && $peopleCount !== null
            && $peopleCount > (int) $menu['stock_quantity']
        ) {
            $errors[] =
                'Le stock disponible permet actuellement de servir '
                . (int) $menu['stock_quantity']
                . ' personne(s) maximum.';
        }

        /* Vérifie la date de la prestation. */
        $eventDateObject = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $eventDate
        );

        $eventDateErrors = \DateTimeImmutable::getLastErrors();

        if (
            $eventDateObject === false
            || (
                is_array($eventDateErrors)
                && (
                    $eventDateErrors['warning_count'] > 0
                    || $eventDateErrors['error_count'] > 0
                )
            )
        ) {
            $errors[] =
                'La date de la prestation est invalide.';
        }

        if (
            $menu !== null
            && $eventDateObject instanceof \DateTimeImmutable
        ) {
            $minimumEventDate = new \DateTimeImmutable(
                '+' . (int) $menu['minimum_order_days'] . ' days'
            );

            $minimumEventDate = $minimumEventDate->setTime(
                0,
                0
            );

            if ($eventDateObject < $minimumEventDate) {
                $errors[] =
                    'Le délai minimum de commande pour ce menu n’est pas respecté.';
            }

            if (!empty($menu['available_from'])) {
                $availableFrom = new \DateTimeImmutable(
                    $menu['available_from']
                );

                if ($eventDateObject < $availableFrom) {
                    $errors[] =
                        'Ce menu n’est pas disponible à cette date.';
                }
            }

            if (!empty($menu['available_until'])) {
                $availableUntil = new \DateTimeImmutable(
                    $menu['available_until']
                );

                if ($eventDateObject > $availableUntil) {
                    $errors[] =
                        'Ce menu n’est plus disponible à cette date.';
                }
            }
        }

        /* Vérifie les créneaux de 10 h à 20 h toutes les 30 minutes. */
        $validTimes = [];

        for (
            $time = strtotime('10:00');
            $time <= strtotime('20:00');
            $time += 30 * 60
        ) {
            $validTimes[] = date('H:i', $time);
        }

        if (!in_array($deliveryTime, $validTimes, true)) {
            $errors[] =
                'Le créneau de livraison sélectionné est invalide.';
        }

        if (!empty($errors)) {
            http_response_code(422);

            $pageTitle = 'Commande invalide';

            $errorTitle = 'Commande invalide';

            $errorMessage =
                'Votre commande n’a pas été enregistrée. '
                . 'Veuillez corriger les informations indiquées ci-dessous.';

            $returnUrl =
                BASE_URL
                . '/order/create?menu_id='
                . (int) $menuId;

            $openingHours = OpeningHour::getAll();

            $view = BASE_PATH . '/app/Views/orders/error.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Recalcule la distance côté serveur. */
        $startLatitude = 44.8378;
        $startLongitude = -0.5792;

        $distance = DeliveryService::calculateDistance(
            $startLatitude,
            $startLongitude,
            (float) $deliveryLatitude,
            (float) $deliveryLongitude
        );

        if ($distance === null) {
            http_response_code(502);

            echo 'La distance de livraison n’a pas pu être recalculée.';

            return;
        }

        $deliveryFees = DeliveryService::calculateDeliveryFees(
            $distance,
            $deliveryCity
        );

        /* Recalcule le prix du menu côté serveur. */
        $minimumPeople = (int) $menu['minimum_people'];
        $basePrice = (float) $menu['base_price'];
        $pricePerPerson = $basePrice / $minimumPeople;

        $menuPrice = $pricePerPerson * (int) $peopleCount;

        $discountApplies =
            (int) $peopleCount >= $minimumPeople + 5;

        if ($discountApplies) {
            $menuPrice *= 0.90;
        }

        $menuPrice = round($menuPrice, 2);
        $deliveryPrice = round(
            (float) $deliveryFees['delivery_fee'],
            2
        );

        $totalPrice = round(
            $menuPrice + $deliveryPrice,
            2
        );

        /* Recharge les données du client depuis la base. */
        $user = User::findById(
            (int) $_SESSION['user']['id']
        );

        if ($user === null) {
            $_SESSION = [];

            session_destroy();

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        /* Enregistre la commande dans une transaction. */
        $createdOrder = Order::create([
            'user_id' => (int) $user['id'],
            'menu_id' => (int) $menu['id'],
            'customer_first_name' => $user['first_name'],
            'customer_last_name' => $user['last_name'],
            'customer_email' => $user['email'],
            'customer_phone' => $user['phone'],
            'delivery_address' => $deliveryAddress,
            'delivery_postal_code' => $deliveryPostalCode,
            'delivery_city' => $deliveryCity,
            'event_date' => $eventDate,
            'delivery_time' => $deliveryTime,
            'people_count' => (int) $peopleCount,
            'menu_price' => $menuPrice,
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
        ]);

        if ($createdOrder === null) {
            http_response_code(500);

            echo '<h1>Commande non enregistrée</h1>';
            echo '<p>'
                . 'Une erreur est survenue ou le menu n’est plus disponible.'
                . '</p>';
            echo '<p><a href="'
                . BASE_URL
                . '/order/create?menu_id='
                . (int) $menuId
                . '">Retour au formulaire</a></p>';

            return;
        }

        /* Empêche la réutilisation du même jeton CSRF. */
        unset($_SESSION['order_csrf_token']);

        $confirmation = [
            'order_number' => $createdOrder['order_number'],
            'status_name' => $createdOrder['status_name'],
            'customer_first_name' => $user['first_name'],
            'customer_email' => $user['email'],
            'menu_title' => $menu['title'],
            'people_count' => (int) $peopleCount,
            'event_date' => $eventDate,
            'delivery_time' => $deliveryTime,
            'delivery_address' => $deliveryAddress,
            'delivery_postal_code' => $deliveryPostalCode,
            'delivery_city' => $deliveryCity,
            'menu_price' => $menuPrice,
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
        ];

        /*
         * La commande reste enregistrée même si l'envoi du mail
         * échoue.
         */
        $confirmation['email_sent'] =
            MailService::sendOrderConfirmationEmail(
                $user['email'],
                $confirmation
            );

        $_SESSION['order_confirmation'] = $confirmation;

        header(
            'Location: '
                . BASE_URL
                . '/order/confirmation'
        );

        exit;
    }
    /* -------------------------------------------------- */
    /* confirmation de commande */
    /* -------------------------------------------------- */

    /* Affiche la confirmation après enregistrement. */
    public function confirmation(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (empty($_SESSION['order_confirmation'])) {
            header('Location: ' . BASE_URL . '/menus');
            exit;
        }

        $confirmation = $_SESSION['order_confirmation'];

        /*
         * La confirmation est affichée une seule fois.
         * Un rafraîchissement redirigera ensuite vers les menus.
         */
        unset($_SESSION['order_confirmation']);

        $pageTitle = 'Confirmation de commande';
        $openingHours = OpeningHour::getAll();

        $view =
            BASE_PATH
            . '/app/Views/orders/confirmation.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}
