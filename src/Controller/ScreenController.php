<?php
require_once __DIR__ . '/../Core/Controller.php';

class ScreenController extends Controller
{
    private $screenManager;
    private $zoneManager;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::requireAuth(); // Tous les utilisateurs connectés peuvent voir

        require_once __DIR__ . '/../Repository/ScreenRepository.php';
        require_once __DIR__ . '/../Repository/ZoneRepository.php';
        $this->screenManager = new ScreenRepository();
        $this->zoneManager = new ZoneRepository();
    }

    public function index()
    {
        $screens = $this->screenManager->findAll();
        $zones = $this->zoneManager->findAll();

        $data = [
            'screens' => $screens,
            'zones' => $zones,
            'success' => $_SESSION['screen_success'] ?? null,
            'error' => $_SESSION['screen_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['screen_success'], $_SESSION['screen_error']);

        $this->render('screens/index', $data);
    }

    public function store()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/screens');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $zoneId = (int) ($_POST['zone_id'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name)) {
            $_SESSION['screen_error'] = 'Le nom de l\'écran est obligatoire';
            $this->redirect('/screens');
            return;
        }

        $screenType = trim($_POST['screen_type'] ?? 'Standard');
        $resolution = trim($_POST['resolution'] ?? '1920x1080');
        $orientation = trim($_POST['orientation'] ?? 'landscape');

        if ($zoneId <= 0) {
            $_SESSION['screen_error'] = 'Veuillez sélectionner une zone';
            $this->redirect('/screens');
            return;
        }

        $data = [
            'name' => $name,
            'zone_id' => $zoneId,
            'location' => $location,
            'screen_type' => $screenType,
            'resolution' => $resolution,
            'orientation' => $orientation,
            'is_active' => $isActive
        ];

        if ($this->screenManager->create($data)) {
            $this->audit->log('CREATE', 'screen', 0, "Ajout de l'écran '{$name}'");
            $_SESSION['screen_success'] = 'Écran ajouté avec succès';
        } else {
            $_SESSION['screen_error'] = 'Erreur lors de l\'ajout';
        }

        $this->redirect('/screens');
    }

    public function update()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/screens');
            return;
        }

        $id = (int) ($_POST['screen_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $zoneId = (int) ($_POST['zone_id'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($name)) {
            $_SESSION['screen_error'] = 'Données invalides';
            $this->redirect('/screens');
            return;
        }

        $screenType = trim($_POST['screen_type'] ?? 'Standard');
        $resolution = trim($_POST['resolution'] ?? '1920x1080');
        $orientation = trim($_POST['orientation'] ?? 'landscape');

        if ($zoneId <= 0) {
            $_SESSION['screen_error'] = 'Veuillez sélectionner une zone';
            $this->redirect('/screens');
            return;
        }

        $data = [
            'name' => $name,
            'zone_id' => $zoneId,
            'location' => $location,
            'screen_type' => $screenType,
            'resolution' => $resolution,
            'orientation' => $orientation,
            'is_active' => $isActive
        ];

        if ($this->screenManager->update($id, $data)) {
            $this->audit->log('UPDATE', 'screen', $id, "Modification de l'écran '{$name}'");
            $_SESSION['screen_success'] = 'Écran modifié avec succès';
        } else {
            $_SESSION['screen_error'] = 'Erreur lors de la modification';
        }

        $this->redirect('/screens');
    }

    public function delete()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/screens');
            return;
        }

        $id = (int) ($_POST['screen_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['screen_error'] = 'ID invalide';
            $this->redirect('/screens');
            return;
        }

        if ($this->screenManager->delete($id)) {
            $this->audit->log('DELETE', 'screen', $id, "Suppression d'un écran");
            $_SESSION['screen_success'] = 'Écran supprimé avec succès';
        } else {
            $_SESSION['screen_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect('/screens');
    }

    private function requireEditorOrAdmin()
    {
        // Seuls Editor et Admin peuvent modifier
        // Si la méthode requireEditor inclut Admin, c'est bon. 
        // Vérifions AuthMiddleware: requireEditor appelle requireRole('editor'). 
        // hasPermission('admin', 'editor') devrait être true.
        AuthMiddleware::requireRole('editor');
    }

    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['screen_error'] = 'Session expirée (CSRF)';
            $this->redirect('/screens');
            exit;
        }
    }
}
