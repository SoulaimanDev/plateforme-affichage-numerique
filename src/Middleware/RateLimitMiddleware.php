<?php

class RateLimitMiddleware
{
    private $db;
    private $limit;
    private $window; // en secondes

    public function __construct($limit = 60, $window = 60)
    {
        $this->db = Database::getInstance()->getConnection();
        $this->limit = $limit;
        $this->window = $window;
    }

    public function handle()
    {
        // Ne limiter que les routes API pour l'instant (ou global si souhaité)
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/api/') !== 0) {
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        try {
            // Nettoyage des vieux enregistrements (GC simple, à optimiser sur gros trafic)
            // On le fait aléatoirement pour ne pas charger chaque requête (1 chance sur 100)
            if (rand(1, 100) === 1) {
                $this->db->query("DELETE FROM api_rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
            }

            // Vérifier le nombre de requêtes dans la fenêtre actuelle
            // On utilise une approche "Sliding Window" simplifiée : compter les enregistrements récents
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM api_rate_limits 
                WHERE ip_address = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$ip, $this->window]);
            $count = $stmt->fetchColumn();

            if ($count >= $this->limit) {
                http_response_code(429);
                header('Retry-After: ' . $this->window);
                die(json_encode([
                    'error' => 'Too Many Requests',
                    'message' => 'Veuillez patienter avant de refaire une requête.'
                ]));
            }

            // Enregistrer la requête
            $insert = $this->db->prepare("INSERT INTO api_rate_limits (ip_address) VALUES (?)");
            $insert->execute([$ip]);

        } catch (PDOException $e) {
            // En cas d'erreur DB (table manquante ?), on laisse passer pour éviter le déni de service auto-infligé
            // error_log("RateLimit DB Error: " . $e->getMessage());
        }
    }
}
