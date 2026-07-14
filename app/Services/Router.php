<?php

namespace App\Services;

class Router
{
    private array $routes = [];

    /* -------------------------------------------------- */
    /* routes GET */
    /* -------------------------------------------------- */

    /* Enregistre une route affichée en GET. */
    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    /* -------------------------------------------------- */
    /* routes POST */
    /* -------------------------------------------------- */

    /* Enregistre une route envoyée par formulaire. */
    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    /* -------------------------------------------------- */
    /* exécution de la route */
    /* -------------------------------------------------- */

    /* Trouve la route demandée et lance le bon contrôleur. */
    public function dispatch(string $uri): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
            $path = substr($path, strlen(BASE_URL));
        }

        $path = '/' . trim($path, '/');

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);

            echo '<h1>Erreur 404</h1>';
            echo '<p>La page demandée est introuvable.</p>';

            return;
        }

        [$controllerClass, $methodName] = $this->routes[$method][$path];

        $controller = new $controllerClass();

        $controller->$methodName();
    }
}