<?php

namespace App\Services;

class OrderService
{
    /* -------------------------------------------------- */
    /* validation de la date */
    /* -------------------------------------------------- */

    /* Vérifie la date et la disponibilité du menu. */
    public static function validateEventDate(
        string $eventDate,
        array $menu
    ): array {
        $errors = [];

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
            return [
                'date' => null,
                'errors' => [
                    'La date de la prestation est invalide.',
                ],
            ];
        }

        $minimumEventDate = new \DateTimeImmutable(
            '+' . (int) $menu['minimum_order_days'] . ' days'
        );

        $minimumEventDate = $minimumEventDate->setTime(0, 0);

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

        return [
            'date' => $eventDateObject,
            'errors' => $errors,
        ];
    }

    /* -------------------------------------------------- */
    /* créneaux de livraison */
    /* -------------------------------------------------- */

    /* Retourne les créneaux disponibles toutes les 30 minutes. */
    public static function getDeliveryTimes(): array
    {
        $deliveryTimes = [];

        for (
            $time = strtotime('10:00');
            $time <= strtotime('20:00');
            $time += 30 * 60
        ) {
            $deliveryTimes[] = date('H:i', $time);
        }

        return $deliveryTimes;
    }

    /* Vérifie qu'un créneau de livraison est autorisé. */
    public static function isValidDeliveryTime(
        string $deliveryTime
    ): bool {
        return in_array(
            $deliveryTime,
            self::getDeliveryTimes(),
            true
        );
    }

    /* -------------------------------------------------- */
    /* calcul du prix */
    /* -------------------------------------------------- */

    /* Calcule le prix du menu et la réduction éventuelle. */
    public static function calculateMenuPrice(
        array $menu,
        int $peopleCount
    ): array {
        $minimumPeople = (int) $menu['minimum_people'];
        $basePrice = (float) $menu['base_price'];

        $pricePerPerson = $basePrice / $minimumPeople;
        $menuPrice = $pricePerPerson * $peopleCount;

        $discountApplies =
            $peopleCount >= $minimumPeople + 5;

        if ($discountApplies) {
            $menuPrice *= 0.90;
        }

        return [
            'menu_price' => round($menuPrice, 2),
            'discount_applies' => $discountApplies,
        ];
    }

    /* Calcule le montant total de la commande. */
    public static function calculateTotalPrice(
        float $menuPrice,
        float $deliveryPrice
    ): float {
        return round(
            $menuPrice + $deliveryPrice,
            2
        );
    }
}
