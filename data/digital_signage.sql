-- ============================================================
-- SCHÉMA DE BASE DE DONNÉES - PLATEFORME D'AFFICHAGE NUMÉRIQUE
-- ============================================================
-- Importer ce fichier dans MySQL avant de lancer l'application

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS digital_signage DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE digital_signage;

-- ============================================================
-- TABLE 1: RÔLES (roles)
-- ============================================================
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique du rôle',
    name VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nom du rôle (super_admin, admin)',
    description TEXT COMMENT 'Description du rôle',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 2: UTILISATEURS (users)
-- ============================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique de l\'utilisateur',
    role_id INT NOT NULL COMMENT 'Référence au rôle',
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Adresse email de l\'utilisateur',
    password VARCHAR(255) NOT NULL COMMENT 'Mot de passe hashé',
    firstname VARCHAR(100) NOT NULL COMMENT 'Prénom',
    lastname VARCHAR(100) NOT NULL COMMENT 'Nom',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Statut actif/inactif',
    last_login DATETIME COMMENT 'Dernière connexion',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 3: ZONES (zones)
-- ============================================================
CREATE TABLE zones (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique de la zone',
    name VARCHAR(100) NOT NULL COMMENT 'Nom de la zone',
    description TEXT COMMENT 'Description de la zone',
    location VARCHAR(150) COMMENT 'Localisation géographique',
    color VARCHAR(10) COMMENT 'Couleur pour identification visuelle',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 4: ÉCRANS (screens)
-- ============================================================
CREATE TABLE screens (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique de l\'écran',
    screen_key VARCHAR(100) UNIQUE NOT NULL COMMENT 'Clé unique pour identifier l\'écran auprès du serveur',
    name VARCHAR(100) NOT NULL COMMENT 'Nom de l\'écran',
    zone_id INT NOT NULL COMMENT 'Référence à la zone',
    location VARCHAR(150) COMMENT 'Localisation précise de l\'écran',
    screen_type VARCHAR(50) COMMENT 'Type d\'écran (55", 32", etc)',
    resolution VARCHAR(20) COMMENT 'Résolution (1920x1080, 3840x2160, etc)',
    orientation VARCHAR(20) DEFAULT 'portrait' COMMENT 'Orientation (portrait, landscape)',
    is_active BOOLEAN DEFAULT TRUE,
    last_ping DATETIME COMMENT 'Dernier contact avec le serveur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_zone (zone_id),
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 5: PUBLICS (audiences)
-- ============================================================
CREATE TABLE audiences (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique du public',
    name VARCHAR(100) NOT NULL COMMENT 'Nom du public (Élèves, Professeurs, Parents, Visiteurs)',
    description TEXT COMMENT 'Description du public',
    color VARCHAR(10) COMMENT 'Couleur pour identification',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 6: CONTENUS (contents)
-- ============================================================
CREATE TABLE contents (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique du contenu',
    title VARCHAR(255) NOT NULL COMMENT 'Titre du contenu',
    description TEXT COMMENT 'Description du contenu',
    content_type VARCHAR(50) NOT NULL COMMENT 'Type de contenu (image, video, text)',
    file_path VARCHAR(500) COMMENT 'Chemin du fichier',
    text_content LONGTEXT COMMENT 'Contenu texte si applicable',
    duration INT DEFAULT 30 COMMENT 'Durée d\'affichage en secondes',
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL COMMENT 'Utilisateur créateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_creator (created_by),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 7: CONTENU <-> PUBLICS (content_audiences)
-- ============================================================
CREATE TABLE content_audiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    content_id INT NOT NULL COMMENT 'Référence au contenu',
    audience_id INT NOT NULL COMMENT 'Référence au public',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_content_audience (content_id, audience_id),
    FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
    FOREIGN KEY (audience_id) REFERENCES audiences(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 8: CONTENU <-> ZONES (content_zones)
-- ============================================================
CREATE TABLE content_zones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    content_id INT NOT NULL COMMENT 'Référence au contenu',
    zone_id INT NOT NULL COMMENT 'Référence à la zone',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_content_zone (content_id, zone_id),
    FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 9: PROGRAMMATION (schedules)
-- ============================================================
CREATE TABLE schedules (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique de la programmation',
    content_id INT NOT NULL COMMENT 'Référence au contenu',
    zone_id INT NOT NULL COMMENT 'Zone cible',
    start_date DATE NOT NULL COMMENT 'Date de début',
    end_date DATE NOT NULL COMMENT 'Date de fin',
    start_time TIME NOT NULL COMMENT 'Heure de début',
    end_time TIME NOT NULL COMMENT 'Heure de fin',
    priority INT DEFAULT 50 COMMENT 'Priorité (0-100, 100 = urgent)',
    day_of_week VARCHAR(7) COMMENT 'Jours de la semaine (lundi, mardi, etc)',
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL COMMENT 'Utilisateur créateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_content (content_id),
    INDEX idx_zone (zone_id),
    INDEX idx_dates (start_date, end_date),
    FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 10: PLAYLIST (playlists)
-- ============================================================
CREATE TABLE playlists (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique de la playlist',
    name VARCHAR(255) NOT NULL COMMENT 'Nom de la playlist',
    description TEXT COMMENT 'Description de la playlist',
    zone_id INT NOT NULL COMMENT 'Zone associée',
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL COMMENT 'Utilisateur créateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_zone (zone_id),
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 11: CONTENU <-> PLAYLIST (playlist_contents)
-- ============================================================
CREATE TABLE playlist_contents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    playlist_id INT NOT NULL COMMENT 'Référence à la playlist',
    content_id INT NOT NULL COMMENT 'Référence au contenu',
    order_index INT NOT NULL COMMENT 'Ordre d\'affichage',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 12: JOURNAL DES ACTIONS (audit_logs)
-- ============================================================
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant du journal',
    user_id INT COMMENT 'Utilisateur effectuant l\'action',
    action VARCHAR(100) NOT NULL COMMENT 'Type d\'action (CREATE, UPDATE, DELETE)',
    entity_type VARCHAR(50) NOT NULL COMMENT 'Type d\'entité (content, screen, schedule, etc)',
    entity_id INT COMMENT 'ID de l\'entité modifiée',
    description TEXT COMMENT 'Description détaillée',
    ip_address VARCHAR(45) COMMENT 'Adresse IP',
    user_agent VARCHAR(500) COMMENT 'User-Agent du navigateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_date (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INSERTION DES DONNÉES INITIALES
-- ============================================================

-- Insertion des rôles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrateur - Accès complet'),
('editor', 'Éditeur - Gestion des contenus'),
('viewer', 'Visualiseur - Lecture seule');

-- Insertion d'un super administrateur par défaut
-- Email: admin@example.com | Mot de passe: password (hash bcrypt)
INSERT INTO users (role_id, email, password, firstname, lastname, is_active) VALUES
(1, 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', TRUE),
(2, 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', 'User', TRUE);

-- Insertion de zones de test
INSERT INTO zones (name, description, location, color, is_active) VALUES
('Entrée principale', 'Affichage d\'entrée de l\'école', 'Entrée', '#FF5733', TRUE),
('Couloir principal', 'Affichage du couloir central', 'Couloir', '#33FF57', TRUE),
('Cafeteria', 'Affichage de la cafeteria', 'Cafeteria', '#3357FF', TRUE),
('Salle de réunion', 'Affichage salle de réunion', 'Salle de réunion', '#FF33F5', TRUE),
('Cour de récréation', 'Affichage cour de récréation', 'Cour', '#F5FF33', TRUE);

-- Insertion de publics
INSERT INTO audiences (name, description, color, is_active) VALUES
('Élèves', 'Contenu destiné aux élèves', '#4CAF50', TRUE),
('Professeurs', 'Contenu destiné aux professeurs', '#2196F3', TRUE),
('Parents', 'Contenu destiné aux parents', '#FF9800', TRUE),
('Visiteurs', 'Contenu destiné aux visiteurs', '#9C27B0', TRUE),
('Tous', 'Contenu destiné à tous les publics', '#607D8B', TRUE);

-- Insertion d'écrans de test
INSERT INTO screens (screen_key, name, zone_id, location, screen_type, resolution, orientation, is_active) VALUES
('SCREEN_001', 'Écran Entrée 1', 1, 'Entrée Nord', '55"', '1920x1080', 'landscape', TRUE),
('SCREEN_002', 'Écran Entrée 2', 1, 'Entrée Sud', '32"', '1920x1080', 'landscape', TRUE),
('SCREEN_003', 'Écran Couloir 1', 2, 'Couloir Principal', '43"', '1920x1080', 'portrait', TRUE),
('SCREEN_004', 'Écran Cafeteria', 3, 'Mur Nord Cafeteria', '55"', '1920x1080', 'landscape', TRUE),
('SCREEN_005', 'Écran Réunion', 4, 'Salle de réunion', '65"', '3840x2160', 'landscape', TRUE);

-- Insertion de contenus de test
INSERT INTO contents (title, description, content_type, text_content, duration, is_active, created_by) VALUES
('Bienvenue à l\'école', 'Message de bienvenue', 'text', '<h1>Bienvenue à notre école</h1><p>Nous sommes heureux de vous accueillir!</p>', 15, TRUE, 1),
('Horaires de la cantine', 'Affichage des horaires', 'text', '<h2>Horaires de la cantine</h2><p>Déjeuner: 12h00 - 13h30</p><p>Goûter: 15h00 - 15h30</p>', 30, TRUE, 1),
('Annonce événement', 'Annonce de l\'événement du mois', 'text', '<h2>Grand Événement Scolaire!</h2><p>Vendredi 20 janvier - 14h00</p><p>Amphithéâtre</p>', 20, TRUE, 1),
('Information urgente', 'Message urgent', 'text', '<h2 style="color:red;">⚠️ INFORMATION URGENTE</h2><p>Veuillez consulter l\'annonce importante</p>', 10, TRUE, 1),
('Emploi du temps', 'Affichage des horaires', 'text', '<h2>Horaires du jour</h2><p>08h30 - 10h30: Cours</p><p>10h30 - 10h45: Récréation</p><p>10h45 - 12h00: Cours</p>', 25, TRUE, 1);

-- Insertion des associations contenu-publics
INSERT INTO content_audiences (content_id, audience_id) VALUES
(1, 5), -- Bienvenue pour tous
(2, 1), -- Horaires cantine pour élèves
(2, 4), -- Horaires cantine pour visiteurs
(3, 5), -- Événement pour tous
(4, 5), -- Urgent pour tous
(5, 1); -- Emploi du temps pour élèves

-- Insertion des associations contenu-zones
INSERT INTO content_zones (content_id, zone_id) VALUES
(1, 1), -- Bienvenue à l'entrée
(1, 2), -- Bienvenue au couloir
(2, 3), -- Horaires cantine à la cafeteria
(3, 1), -- Événement à l'entrée
(3, 2), -- Événement au couloir
(4, 1), -- Urgent à l'entrée
(5, 2); -- Emploi du temps au couloir

-- Insertion d'une programmation de test
INSERT INTO schedules (content_id, zone_id, start_date, end_date, start_time, end_time, priority, is_active, created_by) VALUES
(1, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '07:00:00', '18:00:00', 50, TRUE, 1),
(2, 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '11:00:00', '14:00:00', 50, TRUE, 1),
(3, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), '08:00:00', '18:00:00', 70, TRUE, 1),
(4, 1, CURDATE(), CURDATE(), '08:00:00', '12:00:00', 100, TRUE, 1),
(5, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '08:00:00', '17:00:00', 40, TRUE, 1);

-- ============================================================
-- INDEXES SUPPLÉMENTAIRES POUR PERFORMANCE
-- ============================================================
CREATE INDEX idx_user_role ON users(role_id);
CREATE INDEX idx_user_active ON users(is_active);
CREATE INDEX idx_screen_zone ON screens(zone_id);
CREATE INDEX idx_screen_active ON screens(is_active);
CREATE INDEX idx_content_type ON contents(content_type);
CREATE INDEX idx_content_active ON contents(is_active);
CREATE INDEX idx_schedule_active ON schedules(is_active);
CREATE INDEX idx_audit_user ON audit_logs(user_id);
CREATE INDEX idx_audit_date ON audit_logs(created_at);

-- Affichage des tables créées
SHOW TABLES;
SHOW CREATE TABLE users;
