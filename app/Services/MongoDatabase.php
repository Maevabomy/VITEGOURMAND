<?php

namespace App\Services;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use RuntimeException;
use Throwable;

class MongoDatabase
{
    private static ?Manager $manager = null;

    /* -------------------------------------------------- */
    /* connexion MongoDB */
    /* -------------------------------------------------- */

    public static function getManager(): Manager
    {
        if (self::$manager === null) {
            $config =
                require BASE_PATH . '/config/mongodb.php';

            if (empty($config['uri'])) {
                throw new RuntimeException(
                    'La chaîne de connexion MongoDB est manquante.'
                );
            }

            $options = [
                'tls' => true,
            ];

            if (
                !empty($config['tls_ca_file'])
                && file_exists($config['tls_ca_file'])
            ) {
                $options['tlsCAFile'] =
                    $config['tls_ca_file'];
            }

            /* Active le contournement TLS uniquement pour l'environnement local */
            if ($config['allow_invalid_certificates']) {
                $options['tlsAllowInvalidCertificates'] =
                    true;
            }

            try {
                self::$manager = new Manager(
                    $config['uri'],
                    $options
                );
            } catch (Throwable $exception) {
                throw new RuntimeException(
                    'Impossible de préparer la connexion MongoDB.'
                );
            }
        }

        return self::$manager;
    }

    /* -------------------------------------------------- */
    /* nom de la base */
    /* -------------------------------------------------- */

    public static function getDatabaseName(): string
    {
        $config =
            require BASE_PATH . '/config/mongodb.php';

        return $config['database'];
    }

    /* -------------------------------------------------- */
    /* test de connexion */
    /* -------------------------------------------------- */

    public static function ping(): bool
    {
        try {
            $manager =
                self::getManager();

            $command =
                new Command([
                    'ping' => 1,
                ]);

            $manager->executeCommand(
                self::getDatabaseName(),
                $command
            );

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }
}
