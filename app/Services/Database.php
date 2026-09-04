<?php

namespace App\Services;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    /* -------------------------------------------------- */
    /* connexion à la base de données */
    /* -------------------------------------------------- */

    /* Retourne une connexion PDO unique vers la base de données. */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            /* Charge les paramètres de connexion depuis la configuration. */
            $config = require BASE_PATH . '/config/database.php';

            /* Construit la chaîne de connexion MySQL. */
            $dsn = 'mysql:host=' . $config['host']
                . ';port=' . $config['port']
                . ';dbname=' . $config['dbname']
                . ';charset=' . $config['charset'];

            try {
                /* Crée la connexion PDO avec un mode d'erreur sécurisé. */
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE =>
                        PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE =>
                        PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $exception) {
                /* Masque les détails techniques en cas d'échec de connexion. */
                throw new RuntimeException(
                    'Impossible de se connecter à la base de données.'
                );
            }
        }

        /* Réutilise la connexion existante lors des appels suivants. */
        return self::$connection;
    }
}
