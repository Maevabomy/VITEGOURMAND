<?php

namespace App\Services;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->routes[$path] = $action;
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
            $path = substr($path, strlen(BASE_URL));
        }

        $path = '/' . trim($path, '/');

        if (!isset($this->routes[$path])) {
            http_response_code(404);

            echo '<h1>Erreur 404</h1>';
            echo '<p>La page demandée est introuvable.</p>';

            return;
        }

        [$controllerClass, $method] = $this->routes[$path];

        $controller = new $controllerClass();

        $controller->$method();
    }
}