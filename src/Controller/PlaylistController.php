<?php
require_once __DIR__ . '/../Core/Controller.php';

class PlaylistController extends Controller
{
    private $playlistManager;
    private $zoneManager;
    private $contentManager;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::requireAuth();

        require_once __DIR__ . '/../Repository/PlaylistRepository.php';
        require_once __DIR__ . '/../Repository/ZoneRepository.php';
        require_once __DIR__ . '/../Repository/ContentRepository.php';

        $this->playlistManager = new PlaylistRepository();
        $this->zoneManager = new ZoneRepository();
        $this->contentManager = new ContentRepository();
    }

    public function index()
    {
        $playlists = $this->playlistManager->findAll();
        $zones = $this->zoneManager->findAll(); // For the modal dropdown

        $data = [
            'playlists' => $playlists,
            'zones' => $zones,
            'success' => $_SESSION['playlist_success'] ?? null,
            'error' => $_SESSION['playlist_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['playlist_success'], $_SESSION['playlist_error']);

        $this->render('playlists/index', $data);
    }

    public function store()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/playlists');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $zoneId = (int) ($_POST['zone_id'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $createdBy = $_SESSION['user_id'] ?? null;

        if (empty($name)) {
            $_SESSION['playlist_error'] = 'Le nom est obligatoire';
            $this->redirect('/playlists');
            return;
        }

        if ($zoneId <= 0) {
            $_SESSION['playlist_error'] = 'Une zone valide est requise';
            $this->redirect('/playlists');
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'zone_id' => $zoneId,
            'is_active' => $isActive,
            'created_by' => $createdBy
        ];

        if ($this->playlistManager->create($data)) {
            $this->audit->log('CREATE', 'playlist', 0, "Création de la playlist '{$name}'");
            $_SESSION['playlist_success'] = 'Playlist créée avec succès';
        } else {
            $_SESSION['playlist_error'] = 'Erreur lors de la création';
        }

        $this->redirect('/playlists');
    }

    public function update()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/playlists');
            return;
        }

        $id = (int) ($_POST['playlist_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $zoneId = (int) ($_POST['zone_id'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($name)) {
            $_SESSION['playlist_error'] = 'Données invalides';
            $this->redirect('/playlists');
            return;
        }

        if ($zoneId <= 0) {
            $_SESSION['playlist_error'] = 'Une zone valide est requise';
            $this->redirect('/playlists');
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'zone_id' => $zoneId,
            'is_active' => $isActive
        ];

        if ($this->playlistManager->update($id, $data)) {
            $this->audit->log('UPDATE', 'playlist', $id, "Modification de la playlist '{$name}'");
            $_SESSION['playlist_success'] = 'Playlist modifiée avec succès';
        } else {
            $_SESSION['playlist_error'] = 'Erreur lors de la modification';
        }

        $this->redirect('/playlists');
    }

    public function delete()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        $id = (int) ($_POST['playlist_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['playlist_error'] = 'ID invalide';
            $this->redirect('/playlists');
            return;
        }

        if ($this->playlistManager->delete($id)) {
            $this->audit->log('DELETE', 'playlist', $id, "Suppression d'une playlist");
            $_SESSION['playlist_success'] = 'Playlist supprimée avec succès';
        } else {
            $_SESSION['playlist_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect('/playlists');
    }

    public function manage()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/playlists');
            return;
        }

        $playlist = $this->playlistManager->findById($id);
        if (!$playlist) {
            $this->redirect('/playlists');
            return;
        }

        $items = $this->playlistManager->getContents($id);

        // Tous les contenus disponibles pour ajouter
        $allContents = $this->contentManager->findAll();

        $data = [
            'playlist' => $playlist,
            'items' => $items,
            'allContents' => $allContents,
            'success' => $_SESSION['playlist_success'] ?? null,
            'error' => $_SESSION['playlist_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['playlist_success'], $_SESSION['playlist_error']);

        $this->render('playlists/manage', $data);
    }

    public function addContent()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/playlists');
            return;
        }

        $playlistId = (int) ($_POST['playlist_id'] ?? 0);
        $contentId = (int) ($_POST['content_id'] ?? 0);
        $duration = (int) ($_POST['duration'] ?? 10);
        $order = (int) ($_POST['order'] ?? 0);

        if ($playlistId > 0 && $contentId > 0) {
            if ($this->playlistManager->addContent($playlistId, $contentId, $duration, $order)) {
                $_SESSION['playlist_success'] = 'Contenu ajouté à la playlist';
            } else {
                $_SESSION['playlist_error'] = 'Erreur lors de l\'ajout';
            }
        }

        $this->redirect("/playlists/manage?id=$playlistId");
    }

    public function removeContent()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        $playlistId = (int) ($_POST['playlist_id'] ?? 0);
        $contentId = (int) ($_POST['content_id'] ?? 0);

        if ($playlistId > 0 && $contentId > 0) {
            if ($this->playlistManager->removeContent($playlistId, $contentId)) {
                $_SESSION['playlist_success'] = 'Contenu retiré de la playlist';
            } else {
                $_SESSION['playlist_error'] = 'Erreur lors de la suppression';
            }
        }

        $this->redirect("/playlists/manage?id=$playlistId");
    }

    private function requireEditorOrAdmin()
    {
        AuthMiddleware::requireRole('editor');
    }

    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['playlist_error'] = 'Session expirée (CSRF)';
            $this->redirect('/playlists');
            exit;
        }
    }
}
