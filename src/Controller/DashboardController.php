<?php

class DashboardController extends Controller
{
    private $userManager;
    private $contentManager;
    private $screenManager;
    private $zoneManager;
    private $scheduleManager;

    public function __construct()
    {
        require_once __DIR__ . '/../Repository/UserRepository.php';
        require_once __DIR__ . '/../Repository/ContentRepository.php';
        require_once __DIR__ . '/../Repository/ScreenRepository.php';
        require_once __DIR__ . '/../Repository/ZoneRepository.php';
        require_once __DIR__ . '/../Repository/ScheduleRepository.php';

        $this->userManager = new UserRepository();
        $this->contentManager = new ContentRepository();
        $this->screenManager = new ScreenRepository();
        $this->zoneManager = new ZoneRepository();
        $this->scheduleManager = new ScheduleRepository();
    }

    public function index()
    {
        // Vérification authentification
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        // Récupération de l'utilisateur complet depuis la base de données
        $user = $this->userManager->findById($_SESSION['user_id']);

        if (!$user) {
            // Si l'utilisateur n'existe plus en base
            session_destroy();
            $this->redirect('/login');
            return;
        }

        // Compatibilité avec la vue qui attend 'role'
        $user['role'] = $user['role_name'] ?? '';

        // Récupération des statistiques
        $stats = $this->getStats();

        // Données pour les graphiques
        $chartData = $this->getChartData();

        $data = [
            'user' => $user,
            'stats' => $stats,
            'chartData' => $chartData,
            'page_title' => 'Dashboard'
        ];

        $this->render('dashboard/index', $data);
    }

    /**
     * Récupération des statistiques
     */
    private function getStats()
    {
        try {
            // Compter les contenus
            $contents = count($this->contentManager->findAll());

            // Compter les écrans actifs
            $screens = count($this->screenManager->findAll());

            // Compter les utilisateurs
            $users = count($this->userManager->findAll());

            // Compter les zones
            $zones = count($this->zoneManager->findAll());

            return [
                'contents' => $contents,
                'screens' => $screens,
                'users' => $users,
                'zones' => $zones
            ];
        } catch (Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            return [
                'contents' => 0,
                'screens' => 0,
                'users' => 0,
                'zones' => 0
            ];
        }
    }

    /**
     * Données pour les graphiques
     */
    private function getChartData()
    {
        // 1. Stats de diffusion (7 derniers jours par défaut)
        $diffusionsStats = $this->scheduleManager->getDailyStats(7);
        $diffusionsLabels = array_column($diffusionsStats, 'label');
        $diffusionsData = array_column($diffusionsStats, 'count');

        // 2. Répartition par zones
        $zoneStats = $this->screenManager->countActiveByZone();

        $zoneLabels = [];
        $zoneData = [];

        foreach ($zoneStats as $stat) {
            $zoneLabels[] = $stat['zone_name'];
            $zoneData[] = (int) $stat['count'];
        }

        // Si aucune donnée, mettre des valeurs par défaut pour éviter un graphique vide
        if (empty($zoneData)) {
            $zoneLabels = ['Aucun écran actif'];
            $zoneData = [1]; // Valeur fictive pour afficher le rond
        }

        return [
            'diffusions' => $diffusionsData,
            'diffusionLabels' => $diffusionsLabels,
            'zones' => $zoneData,
            'zoneLabels' => $zoneLabels
        ];
    }
}
