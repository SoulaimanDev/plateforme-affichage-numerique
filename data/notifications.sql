-- Table des notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL COMMENT 'Type: content, alert, system, user',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON COMMENT 'Données additionnelles',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de notifications de test
INSERT INTO notifications (user_id, type, title, message, created_at) VALUES
(1, 'content', 'Nouveau contenu', 'Le contenu "Annonce importante" a été ajouté', NOW() - INTERVAL 5 MINUTE),
(1, 'alert', 'Alerte système', 'Écran "Entrée Nord" déconnecté', NOW() - INTERVAL 15 MINUTE),
(1, 'system', 'Mise à jour', 'Nouvelle version disponible', NOW() - INTERVAL 1 HOUR);