<?php

namespace App\Controllers;

use App\Models\Menu;
use App\Models\OpeningHour;
use App\Models\User;
use App\Services\DeliveryService;

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
}
