<?php
require_once __DIR__ . '/../Core/Controller.php';

class ZoneController extends Controller
{
    private $zoneManager;
    private $screenManager;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::requireAuth();

        require_once __DIR__ . '/../Repository/ZoneRepository.php';
        require_once __DIR__ . '/../Repository/ScreenRepository.php';
        $this->zoneManager = new ZoneRepository();
        $this->screenManager = new ScreenRepository();
    }

    public function index()
    {
        // Les zones sont indépendantes, pas liées à un écran spécifique
        // (C'est l'écran qui appartient à une zone, pas l'inverse)
        $zones = $this->zoneManager->findAll();

        $data = [
            'zones' => $zones,
            'success' => $_SESSION['zone_success'] ?? null,
            'error' => $_SESSION['zone_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['zone_success'], $_SESSION['zone_error']);

        $this->render('zones/index', $data);
    }

    public function store()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/zones');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $color = $_POST['color'] ?? '#3b82f6';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name)) {
            $_SESSION['zone_error'] = 'Le nom est obligatoire';
            $this->redirect('/zones');
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'location' => $location,
            'color' => $color,
            'is_active' => $is_active
        ];

        if ($this->zoneManager->create($data)) {
            $_SESSION['zone_success'] = 'Zone ajoutée avec succès';
        } else {
            $_SESSION['zone_error'] = 'Erreur lors de l\'ajout';
        }

        $this->redirect('/zones');
    }

    public function update()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/zones');
            return;
        }

        $id = (int) ($_POST['zone_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $color = $_POST['color'] ?? '#3b82f6';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($name)) {
            $_SESSION['zone_error'] = 'Données invalides';
            $this->redirect('/zones');
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'location' => $location,
            'color' => $color,
            'is_active' => $is_active
        ];

        if ($this->zoneManager->update($id, $data)) {
            $this->audit->log('UPDATE', 'zone', $id, "Modification de la zone '{$name}'");
            $_SESSION['zone_success'] = 'Zone modifiée avec succès';
        } else {
            $_SESSION['zone_error'] = 'Erreur lors de la modification';
        }

        $this->redirect('/zones');
    }

    public function delete()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        $id = (int) ($_POST['zone_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['zone_error'] = 'ID invalide';
            $this->redirect("/zones"); // Changed from "/zones?screen_id=$screenId"
            return;
        }

        if ($this->zoneManager->delete($id)) {
            $this->audit->log('DELETE', 'zone', $id, "Suppression d'une zone");
            $_SESSION['zone_success'] = 'Zone supprimée avec succès';
        } else {
            $_SESSION['zone_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect('/zones');
    }

    private function requireEditorOrAdmin()
    {

        AuthMiddleware::requireRole('editor');
    }

    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['zone_error'] = 'Session expirée';
            $this->redirect('/screens');
            exit;
        }
    }
}
