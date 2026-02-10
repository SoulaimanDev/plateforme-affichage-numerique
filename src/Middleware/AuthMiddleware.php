<?php

/**
 * Middleware d'authentification
 * Protège les routes selon les rôles utilisateurs
 */
class AuthMiddleware
{

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function requireAuth()
    {
        if (file_exists(__DIR__ . '/../Core/SessionManager.php')) {
            require_once __DIR__ . '/../Core/SessionManager.php';
        }

        // Ajustement du chemin pour AuthService
        // __DIR__ est src/Middleware
        $authServicePath = __DIR__ . '/../Service/AuthService.php';
        if (file_exists($authServicePath)) {
            require_once $authServicePath;
        } else {
            // Fallback try
            require_once __DIR__ . '/../../src/Service/AuthService.php';
        }

        SessionManager::start();
        $authService = new AuthService();

        if (!$authService->isLoggedIn()) {
            // Redirection vers login avec URL de retour
            $returnUrl = urlencode($_SERVER['REQUEST_URI'] ?? '/');
            header("Location: " . url('/login?return=' . $returnUrl));
            exit;
        }

        // Vérification expiration session (optionnel)
        if (SessionManager::isExpired()) {
            SessionManager::logout();
            header("Location: " . url('/login?expired=1'));
            exit;
        }
    }

    /**
     * Vérifie si l'utilisateur a le rôle requis
     */
    public static function requireRole($requiredRole)
    {
        self::requireAuth();

        require_once __DIR__ . '/../Service/AuthService.php';
        $authService = new AuthService();

        $userRole = SessionManager::get('user_role');

        if (!$authService->hasPermission($userRole, $requiredRole)) {
            http_response_code(403);
            die('
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>403 - Accès refusé</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f1f5f9; }
        .error-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); text-align: center; }
        h1 { color: #ef4444; margin-bottom: 1rem; }
        p { color: #64748b; margin-bottom: 1.5rem; }
        a { color: #3b82f6; text-decoration: none; font-weight: 500; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="error-card">
        <h1>403 - Accès refusé</h1>
        <p>Vous n\'avez pas les permissions nécessaires pour accéder à cette page.</p>
        <a href="/">Retour au tableau de bord</a>
    </div>
</body>
</html>
            ');
        }
    }

    /**
     * Middleware pour les administrateurs uniquement
     */
    public static function requireAdmin()
    {
        self::requireRole('admin');
    }

    /**
     * Middleware pour les éditeurs et plus
     */
    public static function requireEditor()
    {
        self::requireRole('editor');
    }

    /**
     * Redirection si déjà connecté (pour page login)
     */
    public static function redirectIfAuthenticated()
    {
        if (file_exists(__DIR__ . '/../Core/SessionManager.php')) {
            require_once __DIR__ . '/../Core/SessionManager.php';
        }
        require_once __DIR__ . '/../Service/AuthService.php';

        SessionManager::start();
        $authService = new AuthService();

        if ($authService->isLoggedIn()) {
            header("Location: " . url('/'));
            exit;
        }
    }
}
