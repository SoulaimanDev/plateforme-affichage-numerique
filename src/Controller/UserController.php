<?php

require_once __DIR__ . '/../Core/Controller.php';

class UserController extends Controller
{
    private $userManager;
    private $roleManager;
    private $authService;

    public function __construct()
    {
        // Sécuriser tout le contrôleur pour les admins uniquement
        // Sécuriser tout le contrôleur pour les admins uniquement
        AuthMiddleware::requireRole('admin');

        require_once __DIR__ . '/../Repository/UserRepository.php';
        require_once __DIR__ . '/../Repository/RoleRepository.php';
        require_once __DIR__ . '/../Service/AuthService.php';

        $this->userManager = new UserRepository();
        $this->roleManager = new RoleRepository();
        $this->authService = new AuthService();
    }

    /**
     * Liste des utilisateurs
     */
    public function index()
    {
        $users = $this->userManager->findAll();
        $roles = $this->roleManager->findAll();

        // Enrichir les données utilisateurs avec le nom du rôle si nécessaire
        // (supposant que la requête findAll le fait déjà via jointure, sinon le faire ici)

        $data = [
            'users' => $users,
            'roles' => $roles,
            'success' => $_SESSION['user_success'] ?? null,
            'error' => $_SESSION['user_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        // Sauvegarder le token CSRF
        $_SESSION['csrf_token'] = $data['csrf_token'];

        // Nettoyer les messages flash
        unset($_SESSION['user_success'], $_SESSION['user_error']);

        $this->render('users/index', $data);
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $roles = $this->roleManager->findAll();

        $data = [
            'roles' => $roles,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];

        $this->render('users/create', $data);
    }

    /**
     * Sauvegarde d'un nouvel utilisateur
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users');
            return;
        }

        $this->checkCsrf();

        // Validation des données
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int) ($_POST['role_id'] ?? 0);

        $errors = [];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        if (empty($firstname) || empty($lastname)) {
            $errors[] = 'Nom et prénom requis';
        }
        if ($roleId <= 0) {
            $errors[] = 'Rôle invalide';
        }

        if ($this->userManager->emailExists($email)) {
            $errors[] = 'Cet email est déjà utilisé';
        }

        if (!empty($errors)) {
            $_SESSION['user_error'] = implode('<br>', $errors);
            $this->redirect('/users/create');
            return;
        }

        // Création
        $userData = [
            'email' => $email,
            'password' => $this->authService->hashPassword($password),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'role_id' => $roleId,
            'is_active' => 1
        ];

        if ($this->userManager->create($userData)) {
            $_SESSION['user_success'] = 'Utilisateur créé avec succès';
        } else {
            $_SESSION['user_error'] = 'Erreur lors de la création en base de données';
        }

        $this->redirect('/users');
    }

    /**
     * Modification d'un utilisateur
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users');
            return;
        }

        $this->checkCsrf();

        $userId = (int) ($_POST['user_id'] ?? 0);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if ($userId <= 0) {
            $_SESSION['user_error'] = 'Utilisateur introuvable';
            $this->redirect('/users');
            return;
        }

        // Vérifier existence user
        $existingUser = $this->userManager->findById($userId);
        if (!$existingUser) {
            $_SESSION['user_error'] = 'Utilisateur introuvable';
            $this->redirect('/users');
            return;
        }

        // Validation basique
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['user_error'] = 'Email invalide';
            $this->redirect('/users');
            return;
        }

        // Unicité email (si changé)
        if ($email !== $existingUser['email'] && $this->userManager->emailExists($email)) {
            $_SESSION['user_error'] = 'Cet email est déjà utilisé par un autre compte';
            $this->redirect('/users');
            return;
        }

        $updateData = [
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'role_id' => $roleId
        ];

        // Mise à jour mot de passe si fourni
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['user_error'] = 'Le mot de passe est trop court (min 6 caractères)';
                $this->redirect('/users');
                return;
            }
            $updateData['password'] = $this->authService->hashPassword($password);
        }

        if ($this->userManager->update($userId, $updateData)) {
            $_SESSION['user_success'] = 'Utilisateur mis à jour avec succès';
        } else {
            $_SESSION['user_error'] = 'Erreur lors de la mise à jour';
        }

        $this->redirect('/users');
    }

    /**
     * Toggle statut (AJAX ou Form)
     */
    public function toggleStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users');
            return;
        }

        $this->checkCsrf();

        $userId = (int) ($_POST['user_id'] ?? 0);
        $isActive = (int) ($_POST['is_active'] ?? 0);

        if ($userId === (int) $_SESSION['user_id']) {
            $_SESSION['user_error'] = 'Vous ne pouvez pas désactiver votre propre compte';
            $this->redirect('/users');
            return;
        }

        if ($this->userManager->updateStatus($userId, $isActive)) {
            $_SESSION['user_success'] = 'Statut mis à jour';
        } else {
            $_SESSION['user_error'] = 'Erreur lors de la mise à jour du statut';
        }

        $this->redirect('/users');
    }

    /**
     * Suppression
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users');
            return;
        }

        $this->checkCsrf();

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId === (int) $_SESSION['user_id']) {
            $_SESSION['user_error'] = 'Vous ne pouvez pas supprimer votre propre compte';
            $this->redirect('/users');
            return;
        }

        if ($this->userManager->delete($userId)) {
            $_SESSION['user_success'] = 'Utilisateur supprimé';
        } else {
            $_SESSION['user_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect('/users');
    }

    /**
     * Helper pour vérifier le CSRF
     */
    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['user_error'] = 'Session expirée ou invalide (CSRF mismatch)';
            $this->redirect('/users');
            exit;
        }
    }
}
