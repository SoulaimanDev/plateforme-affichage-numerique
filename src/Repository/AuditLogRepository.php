<?php
require_once __DIR__ . '/BaseRepository.php';

class AuditLogRepository extends BaseRepository
{
    /**
     * Récupère les logs récents avec les infos utilisateur
     */
    public function findRecent($limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.email as user_email, u.firstname, u.lastname 
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre une nouvelle action
     */
    public function log($userId, $action, $entityType, $entityId, $description = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
