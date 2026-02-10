<?php
require_once __DIR__ . '/../Core/Controller.php';

class PlayerController extends Controller
{
    private $playerService;

    public function __construct()
    {
        parent::__construct();
        // Pas d'AuthMiddleware ici, c'est public mais protégé par la clé

        require_once __DIR__ . '/../Service/PlayerService.php';
        $this->playerService = new PlayerService();
    }

    public function show($key)
    {
        // Headers pour autoriser l'autoplay (tentative côté serveur)
        header("Permissions-Policy: autoplay=*, camera=(), microphone=()");
        header("Feature-Policy: autoplay *");

        if (empty($key)) {
            http_response_code(400);
            echo "Clé d'écran manquante";
            return;
        }

        $screen = $this->playerService->getScreen($key);

        if (!$screen) {
            http_response_code(404);
            // On affiche une vue d'erreur spécifique ou un message simple
            echo "Écran introuvable ou inactif";
            return;
        }

        $content = $this->playerService->getContentForScreen($screen);

        $data = [
            'screen' => $screen,
            'content' => $content,
        ];

        $this->render('player/screen', $data);
    }

    /**
     * Endpoint pour la synchro PWA (JSON pur)
     */
    public function json($key)
    {
        header('Content-Type: application/json');

        if (empty($key)) {
            http_response_code(400);
            echo json_encode(['error' => 'Key missing']);
            return;
        }

        $screen = $this->playerService->getScreen($key);
        if (!$screen) {
            http_response_code(404);
            echo json_encode(['error' => 'Screen not found']);
            return;
        }

        $content = $this->playerService->getContentForScreen($screen);
        echo json_encode($content);
    }
}
