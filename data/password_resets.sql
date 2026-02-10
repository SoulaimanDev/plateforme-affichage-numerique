-- ============================================================
-- TABLE : PASSWORD_RESETS
-- Gestion des tokens de réinitialisation sécurisés sans email
-- ============================================================

-- Suppression de la table si elle existe déjà (pour reset propre)
DROP TABLE IF EXISTS password_resets;

CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL COMMENT 'Utilisateur concerné',
    token_hash VARCHAR(64) NOT NULL COMMENT 'Hash SHA-256 du token',
    expires_at DATETIME NOT NULL COMMENT 'Date d''expiration (15 min)',
    used_at DATETIME NULL COMMENT 'Date d''utilisation (null si non utilisé)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_ip VARCHAR(45) COMMENT 'IP du demandeur',
    user_agent VARCHAR(255) COMMENT 'Navigateur du demandeur',
    
    -- Index pour la recherche rapide par hash et performance
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires (expires_at),
    INDEX idx_user_active (user_id),
    
    -- Contrainte d'intégrité : suppression des resets si l'user est supprimé
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- M procédure stockée ou event pour nettoyer les tokens expirés (optionnel, mais recommandé)
-- Ici, on assume que le nettoyage se fera via PHP lors de la création d'un nouveau token.

-- Ajout d'une colonne 'reset_attempts' dans users pour le Rate Limiting (optionnel mais +sécurisé)
-- ALTER TABLE users ADD COLUMN reset_attempts INT DEFAULT 0;
-- ALTER TABLE users ADD COLUMN last_reset_attempt DATETIME NULL;
