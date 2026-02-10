<?php
require_once __DIR__ . '/BaseRepository.php';

class UserRepository extends BaseRepository
{
    /**
     * Trouve un utilisateur par email avec son rôle
     */
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND u.is_active = 1
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour la dernière connexion
     */
    public function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    /**
     * Trouve tous les utilisateurs
     */
    public function findAll()
    {
        $stmt = $this->db->query("
            SELECT u.*, r.name as role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un utilisateur par ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password, firstname, lastname, role_id, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['email'],
            $data['password'], // Déjà hashé
            $data['firstname'],
            $data['lastname'],
            $data['role_id'],
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }

        $values[] = $id;

        $stmt = $this->db->prepare("
            UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?
        ");

        return $stmt->execute($values);
    }

    /**
     * Met à jour le statut d'un utilisateur
     */
    public function updateStatus($id, $isActive)
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        return $stmt->execute([$isActive, $id]);
    }

    /**
     * Supprime un utilisateur définitivement de la base de données
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Sauvegarde un token de réinitialisation
     */
    public function saveResetToken($userId, $token, $expires)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET reset_token = ?, reset_expires = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$token, $expires, $userId]);
        } catch (PDOException $e) {
            // Si les colonnes n'existent pas, les créer
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                $this->createResetColumns();
                // Réessayer
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET reset_token = ?, reset_expires = ? 
                    WHERE id = ?
                ");
                return $stmt->execute([$token, $expires, $userId]);
            }
            throw $e;
        }
    }

    /**
     * Trouve un utilisateur par token de réinitialisation
     */
    public function findByResetToken($token)
    {
        // Vérifier si les colonnes existent
        try {
            $stmt = $this->db->prepare("
                SELECT id as user_id, email, reset_expires 
                FROM users 
                WHERE reset_token = ? AND reset_expires > NOW()
            ");
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Si les colonnes n'existent pas, les créer automatiquement
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                $this->createResetColumns();
                // Réessayer
                $stmt = $this->db->prepare("
                    SELECT id as user_id, email, reset_expires 
                    FROM users 
                    WHERE reset_token = ? AND reset_expires > NOW()
                ");
                $stmt->execute([$token]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            throw $e;
        }
    }

    /**
     * Crée les colonnes de réinitialisation si elles n'existent pas
     */
    private function createResetColumns()
    {
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL");
            $this->db->exec("ALTER TABLE users ADD COLUMN reset_expires TIMESTAMP NULL");
            $this->db->exec("CREATE INDEX idx_reset_token ON users(reset_token)");
        } catch (PDOException $e) {
            // Ignorer si les colonnes existent déjà
        }
    }

    /**
     * Met à jour le mot de passe
     */
    public function updatePassword($userId, $hashedPassword)
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password = ?, reset_token = NULL, reset_expires = NULL 
            WHERE id = ?
        ");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Supprime le token de réinitialisation
     */
    public function clearResetToken($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET reset_token = NULL, reset_expires = NULL 
            WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }
}
