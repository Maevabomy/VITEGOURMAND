<?php

declare(strict_types=1);

use App\Services\Router;

/*
|--------------------------------------------------------------------------
| constantes principales
|--------------------------------------------------------------------------
|
| BASE_PATH correspond au chemin absolu vers la racine du projet.
| BASE_URL correspond à l'adresse utilisée dans le navigateur.
|
*/

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

/*
|--------------------------------------------------------------------------
| chargement automatique des classes
|--------------------------------------------------------------------------
|
| Cette fonction recherche automatiquement les classes présentes
| dans le dossier app.
|
| Exemple :
| App\Controllers\HomeController
| devient :
| app/Controllers/HomeController.php
|
*/

spl_autoload_register(function (string $className): void {
    $prefix = 'App\\';

    if (strpos($className, $prefix) !== 0) {
        return;
    }

    $relativeClassName = substr($className, strlen($prefix));

    $filePath = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClassName) . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    }
});

/*
|--------------------------------------------------------------------------
| démarrage du routeur
|--------------------------------------------------------------------------
*/

$router = new Router();

require BASE_PATH . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI']);