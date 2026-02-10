<?php
require_once __DIR__ . '/../Core/Controller.php';

class AudienceController extends Controller
{
    private $audienceManager;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::requireAuth();

        require_once __DIR__ . '/../Repository/AudienceRepository.php';
        $this->audienceManager = new AudienceRepository();
    }

    public function index()
    {
        $audiences = $this->audienceManager->findAll();

        $data = [
            'audiences' => $audiences,
            'success' => $_SESSION['audience_success'] ?? null,
            'error' => $_SESSION['audience_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['audience_success'], $_SESSION['audience_error']);

        $this->render('audiences/index', $data);
    }

    public function store()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/audiences');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $color = trim($_POST['color'] ?? '#3b82f6');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name)) {
            $_SESSION['audience_error'] = 'Le nom du public est obligatoire';
            $this->redirect('/audiences');
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'is_active' => $isActive
        ];

        if ($this->audienceManager->create($data)) {
            $this->audit->log('CREATE', 'audience', 0, "Création du public '{$name}'");
            $_SESSION['audience_success'] = 'Public créé avec succès';
        } else {
            $_SESSION['audience_error'] = 'Erreur lors de la création';
        }

        $this->redirect('/audiences');
    }

    public function update()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/audiences');
            return;
        }

        $id = (int) ($_POST['audience_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $color = trim($_POST['color'] ?? '#3b82f6');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($name)) {
            $_SESSION['audience_error'] = 'Données invalides';
            $this->redirect('/audiences');
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'is_active' => $isActive
        ];

        if ($this->audienceManager->update($id, $data)) {
            $this->audit->log('UPDATE', 'audience', $id, "Modification du public '{$name}'");
            $_SESSION['audience_success'] = 'Public modifié avec succès';
        } else {
            $_SESSION['audience_error'] = 'Erreur lors de la modification';
        }

        $this->redirect('/audiences');
    }

    public function delete()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        $id = (int) ($_POST['audience_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['audience_error'] = 'ID invalide';
            $this->redirect('/audiences');
            return;
        }

        if ($this->audienceManager->delete($id)) {
            $this->audit->log('DELETE', 'audience', $id, "Suppression d'un public");
            $_SESSION['audience_success'] = 'Public supprimé avec succès';
        } else {
            $_SESSION['audience_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect('/audiences');
    }

    private function requireEditorOrAdmin()
    {
        AuthMiddleware::requireRole('editor');
    }

    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['audience_error'] = 'Session expirée (CSRF)';
            $this->redirect('/audiences');
            exit;
        }
    }
}
