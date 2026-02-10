<?php
require_once __DIR__ . '/BaseRepository.php';

class NotificationRepository extends BaseRepository
{

    /**
     * Crée une nouvelle notification
     */
    public function create($userId, $type, $title, $message, $data = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, message, data, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            $data ? json_encode($data) : null
        ]);
    }

    /**
     * Récupère les notifications non lues d'un utilisateur
     */
    public function getUnreadByUser($userId, $limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead($notificationId, $userId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notificationId, $userId]);
    }

    /**
     * Marque toutes les notifications comme lues
     */
    public function markAllAsRead($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }

    /**
     * Compte les notifications non lues
     */
    public function countUnread($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    /**
     * Notification pour nouveau contenu
     */
    public function notifyNewContent($contentId, $contentTitle)
    {
        // Notifier tous les admins et editors
        $stmt = $this->db->prepare("
            SELECT u.id FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE r.name IN ('admin', 'editor') AND u.is_active = 1
        ");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $this->create(
                $user['id'],
                'content',
                'Nouveau contenu',
                "Le contenu '{$contentTitle}' a été ajouté",
                ['content_id' => $contentId]
            );
        }
    }

    /**
     * Notification d'alerte système
     */
    public function notifySystemAlert($message, $level = 'warning')
    {
        // Notifier tous les admins
        $stmt = $this->db->prepare("
            SELECT u.id FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE r.name = 'admin' AND u.is_active = 1
        ");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $this->create(
                $user['id'],
                'alert',
                'Alerte système',
                $message,
                ['level' => $level]
            );
        }
    }
}