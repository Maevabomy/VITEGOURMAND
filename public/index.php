<?php

declare(strict_types=1);

use App\Services\Router;


/* -------------------------------------------------- */
/* sécurité de la session */
/* -------------------------------------------------- */

/* Refuse les identifiants de session non générés par PHP. */

ini_set('session.use_strict_mode', '1');

/* Utilise uniquement les cookies pour transmettre l'identifiant de session. */
ini_set('session.use_only_cookies', '1');

/* Détecte si l'application fonctionne en HTTPS, y compris derrière Heroku. */
$isHttps =
    (
        !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off'
    )
    || (
        ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
    );

/* Renforce les paramètres du cookie de session. */
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

/* Crée le jeton CSRF utilisé par les formulaires d'authentification. */
if (empty($_SESSION['auth_csrf_token'])) {
    $_SESSION['auth_csrf_token'] =
        bin2hex(random_bytes(32));
}


/* -------------------------------------------------- */
/* constantes principales */
/* -------------------------------------------------- */

/* Définit les chemins utilisés par l'application. */
define(
    'BASE_PATH',
    dirname(__DIR__)
);

define(
    'BASE_URL',
    rtrim(
        dirname($_SERVER['SCRIPT_NAME']),
        '/'
    )
);


/* -------------------------------------------------- */
/* chargement automatique des classes */
/* -------------------------------------------------- */

/* Charge automatiquement les classes du dossier app. */
spl_autoload_register(
    function (string $className): void {
        $prefix = 'App\\';

        if (strpos($className, $prefix) !== 0) {
            return;
        }

        $relativeClassName = substr(
            $className,
            strlen($prefix)
        );

        $filePath = BASE_PATH
            . '/app/'
            . str_replace(
                '\\',
                '/',
                $relativeClassName
            )
            . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
);


/* -------------------------------------------------- */
/* démarrage du routeur */
/* -------------------------------------------------- */

/* Initialise le routeur de l'application. */
$router = new Router();

/* Charge les routes disponibles. */
require BASE_PATH . '/routes/web.php';

/* Traite la requête HTTP courante. */
$router->dispatch(
    $_SERVER['REQUEST_URI']
);
