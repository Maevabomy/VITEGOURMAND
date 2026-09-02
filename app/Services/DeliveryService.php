<?php

namespace App\Services;

class DeliveryService
{
    private const NOMINATIM_URL =
    'https://nominatim.openstreetmap.org/search';

    private const OSRM_URL =
    'https://router.project-osrm.org/route/v1/driving';

    private const USER_AGENT =
    'ViteEtGourmandECF/1.0 '
        . '(https://github.com/Maevabomy/VITEGOURMAND)';

    /* -------------------------------------------------- */
    /* recherche d'adresse */
    /* -------------------------------------------------- */

    /* Recherche des adresses françaises avec OpenStreetMap. */
    public static function searchAddresses(string $query): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 5) {
            return [];
        }

        $cachedResults = self::readCache($query);

        if ($cachedResults !== null) {
            return $cachedResults;
        }

        $parameters = http_build_query([
            'q' => $query,
            'format' => 'jsonv2',
            'addressdetails' => 1,
            'countrycodes' => 'fr',
            'limit' => 5,
        ]);

        $curl = curl_init(
            self::NOMINATIM_URL . '?' . $parameters
        );

        if ($curl === false) {
            return [];
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: ' . self::USER_AGENT,
            ],
        ]);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (
            $response === false
            || $statusCode !== 200
        ) {
            return [];
        }

        $data = json_decode($response, true);

        if (!is_array($data)) {
            return [];
        }

        $results = [];

        foreach ($data as $address) {
            if (
                empty($address['display_name'])
                || !isset($address['lat'], $address['lon'])
            ) {
                continue;
            }

            $addressDetails =
                $address['address'] ?? [];

            $results[] = [
                'label' => self::buildAddressLabel(
                    $addressDetails,
                    $address['display_name']
                ),
                'latitude' => (float) $address['lat'],
                'longitude' => (float) $address['lon'],
                'postal_code' =>
                $addressDetails['postcode'] ?? '',
                'city' => self::extractCity(
                    $addressDetails
                ),
            ];
        }

        self::saveCache($query, $results);

        return $results;
    }
    /* -------------------------------------------------- */
    /* distance routière */
    /* -------------------------------------------------- */

    /* Calcule la distance routière entre deux coordonnées. */
    public static function calculateDistance(
        float $startLatitude,
        float $startLongitude,
        float $endLatitude,
        float $endLongitude
    ): ?float {
        $coordinates =
            $startLongitude . ',' . $startLatitude
            . ';'
            . $endLongitude . ',' . $endLatitude;

        $parameters = http_build_query([
            'overview' => 'false',
            'steps' => 'false',
        ]);

        $url = self::OSRM_URL
            . '/'
            . $coordinates
            . '?'
            . $parameters;

        $curl = curl_init($url);

        if ($curl === false) {
            return null;
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: ' . self::USER_AGENT,
            ],
        ]);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        curl_close($curl);

        if (
            $response === false
            || $statusCode !== 200
        ) {
            return null;
        }

        $data = json_decode($response, true);

        if (
            !is_array($data)
            || ($data['code'] ?? '') !== 'Ok'
            || empty($data['routes'][0]['distance'])
        ) {
            return null;
        }

        $distanceInMeters = (float) $data['routes'][0]['distance'];

        return round($distanceInMeters / 1000, 2);
    }

    /* -------------------------------------------------- */
    /* frais de livraison */
    /* -------------------------------------------------- */

    /* Calcule le détail des frais de livraison. */
    public static function calculateDeliveryFees(
        float $distance,
        string $city
    ): array {
        $normalizedCity = mb_strtolower(trim($city));

        $isBordeaux = $normalizedCity === 'bordeaux';

        if ($isBordeaux) {
            return [
                'is_bordeaux' => true,
                'fixed_fee' => 0.0,
                'distance_fee' => 0.0,
                'delivery_fee' => 0.0,
            ];
        }

        $fixedFee = 5.0;
        $distanceFee = round($distance * 0.59, 2);
        $deliveryFee = round($fixedFee + $distanceFee, 2);

        return [
            'is_bordeaux' => false,
            'fixed_fee' => $fixedFee,
            'distance_fee' => $distanceFee,
            'delivery_fee' => $deliveryFee,
        ];
    }

    /* -------------------------------------------------- */
    /* formatage de l'adresse */
    /* -------------------------------------------------- */

    /* Construit un libellé court avec la rue, le code postal et la ville. */
    private static function buildAddressLabel(
        array $address,
        string $fallback
    ): string {
        $houseNumber = trim(
            (string) ($address['house_number'] ?? '')
        );

        $road = trim((string) (
            $address['road']
            ?? $address['pedestrian']
            ?? $address['residential']
            ?? $address['footway']
            ?? $address['path']
            ?? ''
        ));

        $postalCode = trim(
            (string) ($address['postcode'] ?? '')
        );

        $city = trim(
            self::extractCity($address)
        );

        $street = trim(
            $houseNumber . ' ' . $road
        );

        $location = trim(
            $postalCode . ' ' . $city
        );

        $parts = array_values(
            array_filter(
                [$street, $location],
                static fn(string $part): bool =>
                $part !== ''
            )
        );

        return !empty($parts)
            ? implode(', ', $parts)
            : $fallback;
    }

    /* -------------------------------------------------- */
    /* ville */
    /* -------------------------------------------------- */

    /* Récupère la ville dans la réponse OpenStreetMap. */
    private static function extractCity(array $address): string
    {
        return $address['city']
            ?? $address['town']
            ?? $address['village']
            ?? $address['municipality']
            ?? '';
    }

    /* -------------------------------------------------- */
    /* cache */
    /* -------------------------------------------------- */

    /* Lit une recherche enregistrée depuis moins de 24 heures. */
    private static function readCache(string $query): ?array
    {
        $cacheFile = self::getCacheFile($query);

        if (
            !is_file($cacheFile)
            || filemtime($cacheFile) < time() - 86400
        ) {
            return null;
        }

        $content = file_get_contents($cacheFile);

        if ($content === false) {
            return null;
        }

        $results = json_decode($content, true);

        return is_array($results) ? $results : null;
    }

    /* Enregistre temporairement les résultats de recherche. */
    private static function saveCache(
        string $query,
        array $results
    ): void {
        $cacheDirectory =
            BASE_PATH . '/storage/cache/geocoding';

        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0775, true);
        }

        file_put_contents(
            self::getCacheFile($query),
            json_encode(
                $results,
                JSON_UNESCAPED_UNICODE
                    | JSON_PRETTY_PRINT
            )
        );
    }

    /* Construit le chemin du fichier de cache. */
    private static function getCacheFile(string $query): string
    {
        return BASE_PATH
            . '/storage/cache/geocoding/'
            . hash('sha256', mb_strtolower(trim($query)))
            . '.json';
    }
}
