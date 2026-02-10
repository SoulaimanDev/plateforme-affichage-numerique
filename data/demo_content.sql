-- Données de démonstration pour l'école "L'Envolée Numérique"
-- À importer après la structure de base (security_updates.sql compris)

-- 0. NETTOYAGE PRÉALABLE (Pour éviter les conflits d'ID)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE schedules;
TRUNCATE TABLE playlist_contents;
TRUNCATE TABLE playlists;
TRUNCATE TABLE content_zones;
TRUNCATE TABLE content_audiences;
TRUNCATE TABLE contents;
TRUNCATE TABLE screens;
TRUNCATE TABLE zones;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. ZONES
INSERT INTO zones (id, name, description, location, color, is_active) VALUES 
(1, 'Hall d\'Entrée', 'Zone d\'accueil principale', 'Bâtiment A, RDC', '#2563eb', 1),
(2, 'Couloir Administration', 'Passage vers secrétariat et bureaux', 'Bâtiment B, 1er Étage', '#10b981', 1),
(3, 'Foyer Élèves / CDI', 'Lieu de vie et de détente', 'Bâtiment C, RDC', '#f59e0b', 1);

-- 2. ÉCRANS
INSERT INTO screens (id, screen_key, name, zone_id, location, screen_type, resolution, orientation, is_active) VALUES
(1, 'SCR_HALL_01', 'TV Accueil', 1, 'Mur principal, face à l\'entrée', 'TV', '1920x1080', 'landscape', 1),
(2, 'SCR_ADMIN_01', 'Écran Info Admin', 2, 'Mur gauche, près porte 101', 'All-in-One', '1920x1080', 'landscape', 1),
(3, 'SCR_FOYER_01', 'TV Foyer', 3, 'Au dessus des canapés', 'TV', '1920x1080', 'landscape', 1);

-- 3. CONTENUS (Images et Vidéos simulées par des textes/placeholders pour la démo technique)

-- Accueil
INSERT INTO contents (id, title, content_type, text_content, file_path, duration, created_by, is_active) VALUES
(1, 'Bienvenue - L\'Envolée', 'text_image', '<h1>Bienvenue à l\'Envolée Numérique</h1>\n"Cultivons l\'excellence, récoltons l\'avenir"', 'uploads/demo_facade.png', 15, 1, 1);

-- Vidéo de Démo (Placeholder)
INSERT INTO contents (id, title, content_type, text_content, file_path, duration, created_by, is_active) VALUES
(7, 'Vidéo Présentation', 'video', NULL, 'uploads/demo_video.mp4', 20, 1, 1);

-- Annonces Admin
INSERT INTO contents (id, title, content_type, text_content, file_path, duration, created_by, is_active) VALUES
(2, 'Horaires Secrétariat', 'text', '<h1>Horaires Secrétariat</h1><p>Lundi - Vendredi : 08h00 - 16h30</p><p>Fermé le mercredi après-midi.</p>', NULL, 10, 1, 1),
(3, 'Menu Cantine', 'text', '<h1>Menu du Jour</h1><ul><li>Entrée : Salade de Chèvre</li><li>Plat : Lasagnes</li><li>Dessert : Pomme Bio</li></ul>', NULL, 10, 1, 1),
(4, 'Rappel Smartphone', 'text', '<h1>Zone Sans Téléphone</h1><p>Merci de respecter la déconnexion dans les couloirs.</p>', NULL, 8, 1, 1);

-- Vie Scolaire
INSERT INTO contents (id, title, content_type, text_content, file_path, duration, created_by, is_active) VALUES
(5, 'Club Robotique', 'text', '<h1>Club Robotique</h1><p>Tous les jeudis midi en salle Techno.</p><p>Venez programmer vos drones !</p>', NULL, 12, 1, 1),
(6, 'Voyage à Rome', 'text', '<h1>Voyage Rome 4ème</h1><p>Réunion d\'info parents : Mardi 18h en salle polyvalente.</p>', NULL, 10, 1, 1);

-- 4. PLAYLISTS (Pour le couloir)
INSERT INTO playlists (id, name, description, created_by, is_active) VALUES
(1, 'Infos Administratives', 'Boucle d\'informations générales', 1, 1);

INSERT INTO playlist_contents (playlist_id, content_id, order_index) VALUES
(1, 2, 1), -- Horaires
(1, 4, 2), -- Smartphone
(1, 3, 3); -- Menu

-- 5. PROGRAMMATION (Schedules)

-- Écran Accueil (Contenu : Bienvenue + Vidéo)
INSERT INTO schedules (id, content_id, playlist_id, zone_id, start_date, end_date, start_time, end_time, day_of_week, priority, is_active, created_by) VALUES
(1, 1, NULL, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '07:00:00', '19:00:00', '123456', 50, 1, 1),
(5, 7, NULL, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '07:00:00', '19:00:00', '123456', 40, 1, 1);

-- Écran Couloir (Playlist Infos)
INSERT INTO schedules (id, content_id, playlist_id, zone_id, start_date, end_date, start_time, end_time, day_of_week, priority, is_active, created_by) VALUES
(2, NULL, 1, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '08:00:00', '18:00:00', '12345', 50, 1, 1);

-- Écran Foyer (Contenus rotatifs simulés par deux programmations horaires ou une playlist, ici simple schedule pour Club)
INSERT INTO schedules (id, content_id, playlist_id, zone_id, start_date, end_date, start_time, end_time, day_of_week, priority, is_active, created_by) VALUES
(3, 5, NULL, 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '12:00:00', '14:00:00', '4', 80, 1, 1), -- Jeudi midi (Prio haute)
(4, 6, NULL, 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '08:00:00', '18:00:00', '12345', 40, 1, 1); -- Voyage Rome (Fond)

