<?php

class PasswordResetController extends Controller
{
    private $userManager;
    private $emailService;

    public function __construct()
    {
        require_once __DIR__ . '/../Repository/UserRepository.php';
        require_once __DIR__ . '/../Service/EmailService.php';

        $this->userManager = new UserRepository();
        $this->emailService = new EmailService();
    }

    /**
     * Affiche le formulaire de demande de réinitialisation
     */
    public function forgot()
    {
        $data = [
            'error' => $_SESSION['reset_error'] ?? null,
            'success' => $_SESSION['reset_success'] ?? null,
            'internal_link' => $_SESSION['internal_link'] ?? null,
            'csrf_token' => $_SESSION['csrf_token']
        ];

        unset($_SESSION['reset_error'], $_SESSION['reset_success'], $_SESSION['internal_link']);

        $this->render('auth/forgot-password', $data);
    }

    /**
     * Traite la demande de réinitialisation (V1 - Table users)
     */
    public function sendReset()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
            return;
        }

        $email = $_POST['email'] ?? '';

        // 1. Vérifier si l'utilisateur existe
        $user = $this->userManager->findByEmail($email);

        if ($user) {
            // 2. Générer un token sécurisé
            $token = bin2hex(random_bytes(32));

            // 3. Définir l'expiration (1 heure)
            $expires = date('Y-m-d H:i:s', time() + 3600);

            // 4. Sauvegarder dans la table `users`
            $this->userManager->saveResetToken($user['id'], $token, $expires);

            // 5. Envoi de l'email
            $link = url('/reset-password?token=' . $token);

            if ($this->emailService->sendResetLink($email, $link)) {
                $_SESSION['reset_success'] = 'Un email contenant le lien de réinitialisation a été envoyé.';
                // $_SESSION['internal_link'] = $link; // Debug removed for production
            } else {
                $_SESSION['reset_success'] = 'Echec de l\'envoi de l\'email, mais un lien a été généré (voir logs).';
                error_log("Mail send failed. Link: " . $link);
            }

            error_log("Password reset requested for {$user['id']} (IP: {$_SERVER['REMOTE_ADDR']})");
        } else {
            // Message générique pour éviter l'énumération des utilisateurs
            $_SESSION['reset_success'] = 'Si cet email existe, vous recevrez un lien de réinitialisation.';
        }

        $this->redirect('/forgot-password');
    }

    /**
     * Affiche le formulaire de nouveau mot de passe
     */
    public function reset()
    {
        $token = $_GET['token'] ?? '';
        $isValid = false;

        if (!empty($token)) {
            // Vérifier token dans la table users
            $user = $this->userManager->findByResetToken($token);
            $isValid = (bool) $user;
        }

        $data = [
            'token' => $token,
            'is_valid' => $isValid,
            'error' => $_SESSION['reset_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token']
        ];

        unset($_SESSION['reset_error']);

        $this->render('auth/reset-password', $data);
    }

    /**
     * Traite la réinitialisation du mot de passe (V1)
     */
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            $_SESSION['reset_error'] = 'Token manquant.';
            $this->redirect('/forgot-password');
            return;
        }

        // 1. Chercher le token valide dans `users`
        $user = $this->userManager->findByResetToken($token);

        if ($user) {
            // Validation mot de passe
            if ($password !== $confirmPassword) {
                $_SESSION['reset_error'] = 'Les mots de passe ne correspondent pas.';
                $this->redirect('/reset-password?token=' . $token);
                return;
            }

            // Complexité : 6 chars min
            if (strlen($password) < 6) {
                $_SESSION['reset_error'] = 'Le mot de passe doit faire au moins 6 caractères.';
                $this->redirect('/reset-password?token=' . $token);
                return;
            }

            // 2. Mise à jour du mot de passe utilisateur
            // Note: updatePassword dans UserRepository vide aussi le token et l'expiration
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $this->userManager->updatePassword($user['user_id'], $hashedPassword);

            $_SESSION['reset_success'] = 'Mot de passe réinitialisé avec succès. Connectez-vous.';

            error_log("Password reset success for user ID {$user['user_id']} (IP: {$_SERVER['REMOTE_ADDR']})");

            $this->redirect('/login');
        } else {
            $_SESSION['reset_error'] = 'Ce lien de réinitialisation est invalide ou expiré.';
            $this->redirect('/reset-password?token=' . $token);
        }
    }
}