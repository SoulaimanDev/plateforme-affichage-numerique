<?php
require_once __DIR__ . '/BaseRepository.php';

class PasswordResetRepository extends BaseRepository
{

    /**
     * Crée un nouveau token de réinitialisation
     * Invalide automatiquement les anciens tokens de l'utilisateur
     */
    public function createToken($userId, $tokenHash, $expiresAt, $ip, $userAgent)
    {
        try {
            $this->db->beginTransaction();

            // 1. Invalider/Supprimer les anciens tokens non utilisés pour cet user
            // On pourrait les marquer comme expirés, mais pour la propreté on supprime les non-utilisés
            $stmtDel = $this->db->prepare("DELETE FROM password_resets WHERE user_id = ? AND used_at IS NULL");
            $stmtDel->execute([$userId]);

            // 2. Insérer le nouveau token
            $stmt = $this->db->prepare("
                INSERT INTO password_resets (user_id, token_hash, expires_at, created_ip, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userId,
                $tokenHash,
                $expiresAt,
                $ip,
                substr($userAgent, 0, 255) // Tronquer si trop long
            ]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error creating reset token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recherche un token valide (non expiré, non utilisé)
     */
    public function findValidToken($tokenHash)
    {
        $stmt = $this->db->prepare("
            SELECT pr.*, u.email 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token_hash = ? 
            AND pr.expires_at > ? 
            AND pr.used_at IS NULL
            AND u.is_active = 1
        ");

        $stmt->execute([$tokenHash, date('Y-m-d H:i:s')]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Marque un token comme utilisé
     */
    public function markAsUsed($tokenId)
    {
        $stmt = $this->db->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
        return $stmt->execute([$tokenId]);
    }

    /**
     * Nettoie les anciens tokens (utilisés ou expirés depuis > 24h)
     */
    public function cleanOldTokens()
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE expires_at < (NOW() - INTERVAL 1 DAY) OR used_at IS NOT NULL");
        return $stmt->execute();
    }
}
