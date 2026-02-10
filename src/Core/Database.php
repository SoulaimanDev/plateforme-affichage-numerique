<?php

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }

    private function handleConnectionError($e)
    {
        // En production, on masque les détails techniques
        $isProd = (defined('APP_ENV') && APP_ENV === 'production') ||
            (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production');

        $message = "Une erreur est survenue lors de la connexion à la base de données.";
        $debugInfo = "";

        if (!$isProd) {
            $message = "Erreur SQL : " . $e->getMessage();
            $debugInfo = "<pre>" . $e->getTraceAsString() . "</pre>";
        }

        // Affichage d'une page d'erreur propre
        http_response_code(503);
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Service Indisponible</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e74c3c; }
        p { color: #666; }
        .retry { margin-top: 20px; display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Service Temporairement Indisponible</h1>
        <p>$message</p>
        $debugInfo
        <a href="javascript:location.reload()" class="retry">Réessayer</a>
    </div>
</body>
</html>
HTML;
        exit;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
