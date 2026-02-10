<?php

/**
 * Service d'authentification sécurisé
 */
class AuthService
{
    private $userManager;

    public function __construct()
    {
        require_once __DIR__ . '/../Repository/UserRepository.php';
        $this->userManager = new UserRepository();
    }

    /**
     * Hash sécurisé du mot de passe
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Vérification du mot de passe
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Authentification complète de l'utilisateur
     */
    public function authenticate($email, $password)
    {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email et mot de passe requis'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email invalide'];
        }

        // Protection Brute Force
        if ($this->isBlocked($email)) {
            return ['success' => false, 'message' => 'Trop de tentatives échouées. Veuillez patienter 15 minutes.'];
        }

        $user = $this->userManager->findByEmail($email);

        // Echec : User Unknown
        if (!$user) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Identifiants invalides'];
        }

        // Echec : Wrong Password
        if (!$this->verifyPassword($password, $user['password'])) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Identifiants invalides'];
        }

        // Succès
        $this->clearFailedAttempts($email);
        $this->userManager->updateLastLogin($user['id']);

        return [
            'success' => true,
            'user' => $user,
            'message' => 'Connexion réussie'
        ];
    }

    /**
     * Vérifie si l'IP ou l'email est bloqué (5 essais en 15 min)
     */
    private function isBlocked($email)
    {
        $db = Database::getInstance()->getConnection();
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM login_attempts 
                WHERE (ip_address = ? OR email = ?) 
                AND is_success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ");
            $stmt->execute([$ip, $email]);
            $count = $stmt->fetchColumn();

            return $count >= 5;
        } catch (Exception $e) {
            // Si la table n'existe pas encore, on ne bloque pas
            return false;
        }
    }

    /**
     * Enregistre une tentative échouée
     */
    private function recordFailedAttempt($email)
    {
        $db = Database::getInstance()->getConnection();
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, email, is_success) VALUES (?, ?, 0)");
            $stmt->execute([$ip, $email]);
        } catch (Exception $e) {
            // Ignorer erreur d'insertion
        }
    }

    /**
     * Nettoie les tentatives après succès
     */
    private function clearFailedAttempts($email)
    {
        $db = Database::getInstance()->getConnection();
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ? OR email = ?");
            $stmt->execute([$ip, $email]);
        } catch (Exception $e) {
            // Ignorer
        }
    }

    /**
     * Vérification des permissions par rôle
     */
    public function hasPermission($userRole, $requiredRole)
    {
        $hierarchy = ['viewer' => 1, 'editor' => 2, 'admin' => 3];

        return isset($hierarchy[$userRole]) &&
            isset($hierarchy[$requiredRole]) &&
            $hierarchy[$userRole] >= $hierarchy[$requiredRole];
    }

    /**
     * Vérification si l'utilisateur est connecté
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }

    /**
     * Récupération des données utilisateur de la session
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
}
