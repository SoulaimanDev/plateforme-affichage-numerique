<?php

class Controller
{
    protected $audit;

    public function __construct()
    {
        require_once __DIR__ . '/../Service/AuditService.php';
        $this->audit = new AuditService();
    }

    protected function render($view, $data = [])
    {
        // Injection du token CSRF global pour toutes les vues
        if (!isset($data['csrf_token'])) {
            $data['csrf_token'] = $_SESSION['csrf_token'] ?? '';
        }

        extract($data);
        require_once __DIR__ . "/../View/{$view}.php";
    }

    protected function redirect($path)
    {
        $url = function_exists('url') ? url($path) : $path;
        header("Location: {$url}");
        exit;
    }
}
