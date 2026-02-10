<?php
require_once __DIR__ . '/../Core/Controller.php';

class ScheduleController extends Controller
{
    private $scheduleManager;
    private $contentManager;
    private $playlistManager;
    private $zoneManager;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::requireAuth();

        require_once __DIR__ . '/../Repository/ScheduleRepository.php';
        require_once __DIR__ . '/../Repository/ContentRepository.php';
        require_once __DIR__ . '/../Repository/PlaylistRepository.php';
        require_once __DIR__ . '/../Repository/ZoneRepository.php';

        $this->scheduleManager = new ScheduleRepository();
        $this->contentManager = new ContentRepository();
        $this->playlistManager = new PlaylistRepository();
        $this->zoneManager = new ZoneRepository();
    }

    public function index()
    {
        $schedules = $this->scheduleManager->findAll();
        $contents = $this->contentManager->findAll();
        $playlists = $this->playlistManager->findAll();
        $zones = $this->zoneManager->findAll();

        $data = [
            'schedules' => $schedules,
            'contents' => $contents,
            'playlists' => $playlists,
            'zones' => $zones,
            'success' => $_SESSION['schedule_success'] ?? null,
            'error' => $_SESSION['schedule_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['schedule_success'], $_SESSION['schedule_error']);

        $this->render('schedules/index', $data);
    }

    public function store()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/schedules');
            return;
        }

        // Récupération et nettoyage des données
        $contentId = (int) ($_POST['content_id'] ?? 0);
        $playlistId = (int) ($_POST['playlist_id'] ?? 0);
        $zoneId = (int) ($_POST['zone_id'] ?? 0);
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $priority = (int) ($_POST['priority'] ?? 50);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Gestion des jours de la semaine (array -> string)
        // Gestion des jours de la semaine (array -> string "1234567")
        $days = $_POST['days'] ?? [];
        // Map days to numbers for storage in VARCHAR(7)
        $dayMap = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];
        $numericDays = [];
        foreach ($days as $day) {
            if (isset($dayMap[$day])) {
                $numericDays[] = $dayMap[$day];
            }
        }
        sort($numericDays); // Ensure consistent order
        $dayOfWeek = implode('', $numericDays);

        $createdBy = $_SESSION['user_id'] ?? null;

        // Validation
        if (($contentId <= 0 && $playlistId <= 0) || $zoneId <= 0) {
            $_SESSION['schedule_error'] = 'Contenu OU Playlist, et Zone obligatoires';
            $this->redirect('/schedules');
            return;
        }

        if ($startDate > $endDate) {
            $_SESSION['schedule_error'] = 'La date de début doit être avant la fin';
            $this->redirect('/schedules');
            return;
        }

        if ($startTime >= $endTime) {
            $_SESSION['schedule_error'] = 'L\'heure de début doit être avant l\'heure de fin';
            $this->redirect('/schedules');
            return;
        }

        if ($priority < 0 || $priority > 100) {
            $_SESSION['schedule_error'] = 'La priorité doit être entre 0 et 100';
            $this->redirect('/schedules');
            return;
        }

        if (empty($days)) {
            $_SESSION['schedule_error'] = 'Sélectionnez au moins un jour';
            $this->redirect('/schedules');
            return;
        }

        $data = [
            'content_id' => $contentId ?: null,
            'playlist_id' => $playlistId ?: null,
            'zone_id' => $zoneId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'day_of_week' => $dayOfWeek,
            'priority' => $priority,
            'is_active' => $isActive,
            'created_by' => $createdBy
        ];

        if ($this->scheduleManager->create($data)) {
            $this->audit->log('CREATE', 'schedule', 0, "Ajout d'une programmation");
            $_SESSION['schedule_success'] = 'Programmation ajoutée avec succès';
        } else {
            $_SESSION['schedule_error'] = 'Erreur lors de l\'ajout';
        }

        $this->redirect('/schedules');
    }

    public function update()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/schedules');
            return;
        }

        $id = (int) ($_POST['schedule_id'] ?? 0);
        $contentId = (int) ($_POST['content_id'] ?? 0);
        $playlistId = (int) ($_POST['playlist_id'] ?? 0);
        $zoneId = (int) ($_POST['zone_id'] ?? 0);
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $priority = (int) ($_POST['priority'] ?? 50);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $days = $_POST['days'] ?? [];
        // Map days to numbers for storage in VARCHAR(7)
        $dayMap = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];
        $numericDays = [];
        foreach ($days as $day) {
            if (isset($dayMap[$day])) {
                $numericDays[] = $dayMap[$day];
            }
        }
        sort($numericDays);
        $dayOfWeek = implode('', $numericDays);

        if ($id <= 0) {
            $_SESSION['schedule_error'] = 'ID invalide';
            $this->redirect('/schedules');
            return;
        }

        // Mêmes validations que store()...
        if (($contentId <= 0 && $playlistId <= 0) || $zoneId <= 0) {
            $_SESSION['schedule_error'] = 'Contenu OU Playlist, et Zone obligatoires';
            $this->redirect('/schedules');
            return;
        }

        if ($startDate > $endDate || $startTime >= $endTime) {
            $_SESSION['schedule_error'] = 'Vérifiez les dates et heures';
            $this->redirect('/schedules');
            return;
        }

        $data = [
            'content_id' => $contentId ?: null,
            'playlist_id' => $playlistId ?: null,
            'zone_id' => $zoneId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'day_of_week' => $dayOfWeek,
            'priority' => $priority,
            'is_active' => $isActive
        ];

        if ($this->scheduleManager->update($id, $data)) {
            $this->audit->log('UPDATE', 'schedule', $id, "Modification d'une programmation");
            $_SESSION['schedule_success'] = 'Programmation modifiée avec succès';
        } else {
            $_SESSION['schedule_error'] = 'Erreur lors de la modification';
        }

        $this->redirect('/schedules');
    }

    public function delete()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        $id = (int) ($_POST['schedule_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['schedule_error'] = 'ID invalide';
            $this->redirect('/schedules');
            return;
        }

        if ($this->scheduleManager->delete($id)) {
            $this->audit->log('DELETE', 'schedule', $id, "Suppression d'une programmation");
            $_SESSION['schedule_success'] = 'Programmation supprimée avec succès';
        } else {
            $_SESSION['schedule_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect('/schedules');
    }

    private function requireEditorOrAdmin()
    {
        AuthMiddleware::requireRole('editor');
    }

    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['schedule_error'] = 'Session expirée (CSRF)';
            $this->redirect('/schedules');
            exit;
        }
    }
}
