<?php

class AuthController extends Controller
{
    private $authService;

    public function __construct()
    {
        require_once __DIR__ . '/../Service/AuthService.php';
        $this->authService = new AuthService();
    }

    public function login()
    {
        // Redirection si déjà connecté
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
            return;
        }

        $data = [
            'error' => $_SESSION['login_error'] ?? null,
            'success' => $_SESSION['login_success'] ?? null,
            'expired' => isset($_GET['expired'])
        ];

        // Nettoyage des messages
        unset($_SESSION['login_error'], $_SESSION['login_success']);

        $this->render('auth/login', $data);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->authService->authenticate($email, $password);

        if ($result['success']) {
            // Régénération de session pour sécurité
            session_regenerate_id(true);

            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_role'] = $result['user']['role_name'];
            $_SESSION['login_time'] = time();

            $returnUrl = $_GET['return'] ?? '/';
            $this->redirect($returnUrl);
        } else {
            $_SESSION['login_error'] = $result['message'];
            $this->redirect('/login');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
