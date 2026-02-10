<?php

class CsrfMiddleware
{
    public function handle()
    {
        // 1. Assurer qu'un token existe toujours en session
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // 2. Vérifier le token pour les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Si c'est un upload sans token (par ex), ou si le token est manquant
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {

                // On peut logger la tentative ici
                error_log("Tentative CSRF détectée depuis " . $_SERVER['REMOTE_ADDR']);

                // Réponse 403 Forbidden
                http_response_code(403);
                die('Erreur de sécurité : session expirée ou requête invalide (CSRF). Veuillez rafraîchir la page.');
            }
        }
    }
}
