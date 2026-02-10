<?php

class Router
{
    private $routes = [];

    public function add($method, $path, $controller, $action, $middleware = [])
    {
        $this->routes[] = compact('method', 'path', 'controller', 'action', 'middleware');
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Gestion des installations en sous-dossier
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        // Normalisation des slashes pour Windows
        $scriptDir = str_replace('\\', '/', $scriptDir);
        // Retirer le slash final pour être propre
        $scriptDir = rtrim($scriptDir, '/');

        // Si l'application est dans un sous-dossier, on retire ce préfixe de l'URI
        if ($scriptDir !== '' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // S'assurer que l'URI commence par /
        if ($uri === '' || $uri === false) {
            $uri = '/';
        } elseif ($uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {

                // Protection CSRF globale
                $csrfMiddleware = new CsrfMiddleware();
                $csrfMiddleware->handle();

                // Rate Limiting API
                require_once __DIR__ . '/../Middleware/RateLimitMiddleware.php';
                $rateLimitMiddleware = new RateLimitMiddleware(60, 60); // 60 requêtes / 60 secondes
                $rateLimitMiddleware->handle();

                // Application des middlewares de sécurité
                $this->applyMiddleware($route['middleware'] ?? []);

                $controllerName = $route['controller'];
                $action = $route['action'];

                require_once __DIR__ . "/../Controller/{$controllerName}.php";
                $controller = new $controllerName();

                // Appel de l'action avec les paramètres
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page non trouvée";
    }

    private function matchPath($routePath, $uri, &$params)
    {
        // Convertir la route en regex (ex: /player/{key} -> #^/player/([^/]+)$#)
        $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([^/]+)', $routePath);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Retirer la correspondance complète
            $params = $matches;
            return true;
        }

        return false;
    }

    /**
     * Application des middlewares de sécurité
     */
    private function applyMiddleware($middleware)
    {
        if (empty($middleware)) {
            return;
        }

        require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

        // Vérification authentification
        if (isset($middleware['auth']) && $middleware['auth']) {
            AuthMiddleware::requireAuth();
        }

        // Vérification rôle
        if (isset($middleware['role'])) {
            AuthMiddleware::requireRole($middleware['role']);
        }
    }
}
