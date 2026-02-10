<?php
require_once __DIR__ . '/../Core/Database.php';

class AuditService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Enregistre une action dans le journal d'audit
     * 
     * @param string $action L'action effectuée (CREATE, UPDATE, DELETE, LOGIN, ERROR)
     * @param string $entityType Le type d'entité concernée (screen, content, user, etc.)
     * @param int|null $entityId L'ID de l'entité
     * @param string|null $description Description détaillée
     * @param int|null $userId ID de l'utilisateur (optionnel, sinon prend celui de la session)
     */
    public function log($action, $entityType, $entityId, $description = null, $userId = null)
    {
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip, :ua)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => strtoupper($action),
            ':entity_type' => $entityType,
            ':entity_id' => $entityId,
            ':description' => $description,
            ':ip' => $ipAddress,
            ':ua' => $userAgent
        ]);
    }

    /**
     * Récupère les logs récents
     */
    public function getRecentLogs($limit = 50)
    {
        $sql = "SELECT l.*, u.email as user_email, u.firstname, u.lastname 
                FROM audit_logs l 
                LEFT JOIN users u ON l.user_id = u.id 
                ORDER BY l.created_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
