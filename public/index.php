<?php
// Autoload Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
} catch (Exception $e) {
    // Si .env n'existe pas, on continue (utile pour la prod qui utilise des vraies ENV vars)
    // Mais ici on veut renforcer l'usage du .env
}

// Config
// Config
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Core/functions.php'; // Helpers globaux (url(), dd())
require_once __DIR__ . '/../src/Core/SessionManager.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../src/Core/Database.php'; // Restore Database
require_once __DIR__ . '/../src/Core/Controller.php';
require_once __DIR__ . '/../src/Repository/BaseRepository.php';
require_once __DIR__ . '/../src/Core/Router.php'; // Restored
require_once __DIR__ . '/../src/Http/Request.php';
require_once __DIR__ . '/../src/Http/Response.php';

session_start();

// Gestionnaire d'erreurs global pour éviter les pages blanches/500
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR)) {
        http_response_code(500);
        if (ini_get('display_errors')) {
            echo "<h1>Fatal Error</h1><pre>" . print_r($error, true) . "</pre>";
        } else {
            echo "<h1>Erreur Serveur</h1><p>Une erreur critique est survenue. Veuillez consulter les logs.</p>";
        }
    }
});

try {
    $router = new Router();
    require_once __DIR__ . '/../config/routes.php';
    $router->dispatch();
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Exception Non Gérée</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    if (ini_get('display_errors')) {
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
