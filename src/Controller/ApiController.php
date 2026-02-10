<?php

class ApiController extends Controller
{
    private $notificationManager;
    private $auditLogManager;
    private $scheduleManager;

    public function __construct()
    {
        require_once __DIR__ . '/../Repository/NotificationRepository.php';
        require_once __DIR__ . '/../Repository/AuditLogRepository.php';
        require_once __DIR__ . '/../Repository/ScheduleRepository.php';
        $this->notificationManager = new NotificationRepository();
        $this->auditLogManager = new AuditLogRepository();
        $this->scheduleManager = new ScheduleRepository();
    }

    /**
     * Stats de diffusion
     */
    public function getDiffusionStats()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non authentifié']);
            return;
        }

        $period = isset($_GET['period']) ? (int) $_GET['period'] : 7;

        // Sécurité : limiter aux valeurs autorisées
        if (!in_array($period, [7, 30])) {
            $period = 7;
        }

        $stats = $this->scheduleManager->getDailyStats($period);

        echo json_encode([
            'labels' => array_column($stats, 'label'),
            'data' => array_column($stats, 'count')
        ]);
    }


    /**
     * API pour récupérer les notifications
     */
    public function notifications()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non authentifié']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $notifications = $this->notificationManager->getUnreadByUser($userId);
        $count = $this->notificationManager->countUnread($userId);

        echo json_encode([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationRead()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $notificationId = $input['id'] ?? null;

        if (!$notificationId || !isset($_SESSION['user_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données invalides']);
            return;
        }

        $success = $this->notificationManager->markAsRead($notificationId, $_SESSION['user_id']);

        echo json_encode(['success' => $success]);
    }

    /**
     * Activité temps réel
     */
    public function activity()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non authentifié']);
            return;
        }

        $logs = $this->auditLogManager->findRecent(10);
        $activities = [];

        foreach ($logs as $log) {
            $activities[] = [
                'id' => $log['id'],
                'type' => $log['entity_type'],
                'message' => $log['description'] ?? $log['action'],
                'user' => $log['firstname'] ? $log['firstname'] . ' ' . $log['lastname'] : ($log['user_email'] ?? 'Système'),
                'time' => date('H:i', strtotime($log['created_at'])),
                'icon' => $this->getIconForType($log['entity_type']),
                'color' => $this->getColorForAction($log['action'])
            ];
        }

        echo json_encode(['activities' => $activities]);
    }

    private function getIconForType($type)
    {
        $icons = [
            'content' => 'fas fa-file-alt',
            'screen' => 'fas fa-desktop',
            'user' => 'fas fa-user-plus',
            'schedule' => 'fas fa-calendar-alt',
            'playlist' => 'fas fa-list',
            'zone' => 'fas fa-map-marker-alt'
        ];
        return $icons[$type] ?? 'fas fa-info-circle';
    }

    private function getColorForAction($action)
    {
        if (stripos($action, 'create') !== false || stripos($action, 'add') !== false)
            return 'success';
        if (stripos($action, 'update') !== false || stripos($action, 'edit') !== false)
            return 'primary';
        if (stripos($action, 'delete') !== false || stripos($action, 'remove') !== false)
            return 'danger';
        return 'info';
    }

    /**
     * Export des statistiques en CSV
     */
    public function exportStats()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            return;
        }

        $format = $_GET['format'] ?? 'csv';

        // Données d'exemple
        $stats = [
            ['Date', 'Contenus', 'Écrans', 'Utilisateurs', 'Diffusions'],
            [date('Y-m-d'), 25, 12, 8, 156],
            [date('Y-m-d', strtotime('-1 day')), 24, 12, 8, 142],
            [date('Y-m-d', strtotime('-2 days')), 23, 11, 7, 138],
        ];

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="stats_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');
            foreach ($stats as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        } else {
            echo json_encode(['error' => 'Format non supporté']);
        }
    }
}