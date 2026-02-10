-- Ajout des colonnes pour la réinitialisation de mot de passe
ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expires TIMESTAMP NULL;

-- Index pour améliorer les performances
CREATE INDEX idx_reset_token ON users(reset_token);
CREATE INDEX idx_reset_expires ON users(reset_expires);

-- Vérification que les colonnes ont été ajoutées
DESCRIBE users;