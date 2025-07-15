-- Gestion d'entreprise de résidence

-- Creation de la base de données avec support UTF8MB4 pour les emojis
CREATE DATABASE IF NOT EXISTS gestion_entreprise_residence 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
USE gestion_entreprise_residence;
DROP TABLE IF EXISTS incident, logs, invite, message_fichier, message_reaction, personal_access_tokens, visite, message, personne_groupe, groupe_message, ban, resident, gardien, admin, personne;


-- TABLE personne
CREATE TABLE personne (
  id_personne INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL UNIQUE,
  nom VARCHAR(45) NOT NULL,
  prenom VARCHAR(45) NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  numero_telephone VARCHAR(20) NOT NULL,
  photo_profil VARCHAR(500) NULL,
  PRIMARY KEY(id_personne),
  UNIQUE KEY unique_email(email)
);

-- TABLE admin (suppression du mot de passe redondant)
CREATE TABLE admin (
  id_admin INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_personne INTEGER UNSIGNED NOT NULL,
  niveau_acces ENUM('super_admin', 'admin_standard') DEFAULT 'admin_standard',
  date_nomination TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_admin),
  UNIQUE KEY(id_personne),
  FOREIGN KEY(id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE
);

-- TABLE gardien
CREATE TABLE gardien (
  id_gardien INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_personne INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(id_gardien),
  INDEX gardien_FKIndex1(id_personne),
  FOREIGN KEY(id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE
);

-- TABLE resident
CREATE TABLE resident (
  id_resident INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_personne INTEGER UNSIGNED NOT NULL,
  adresse_logement VARCHAR(255) NOT NULL,
  PRIMARY KEY(id_resident),
  INDEX resident_FKIndex1(id_personne),
  FOREIGN KEY(id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE
);

-- TABLE ban
CREATE TABLE ban (
  id_ban INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_proprietaire INTEGER UNSIGNED NOT NULL,
  motif TEXT NULL, -- Motif du bannissement (peut être nul)
  date_ban TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_ban),
  INDEX ban_FKIndex1(id_proprietaire),
  FOREIGN KEY(id_proprietaire) REFERENCES personne(id_personne) ON DELETE CASCADE
);

-- TABLE groupe_message
CREATE TABLE groupe_message (
  id_groupe_message INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nom_groupe VARCHAR(100) NULL,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_groupe_message)
);

-- TABLE personne_groupe (liaison many-to-many entre personne et groupe_message)
CREATE TABLE personne_groupe (
  id_personne_groupe INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_personne INTEGER UNSIGNED NOT NULL,
  id_groupe_message INTEGER UNSIGNED NOT NULL,
  date_adhesion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  derniere_connexion TIMESTAMP NULL,
  PRIMARY KEY(id_personne_groupe),
  UNIQUE KEY unique_personne_groupe(id_personne, id_groupe_message),
  INDEX personne_groupe_FKIndex1(id_personne),
  INDEX personne_groupe_FKIndex2(id_groupe_message),
  FOREIGN KEY(id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE,
  FOREIGN KEY(id_groupe_message) REFERENCES groupe_message(id_groupe_message) ON DELETE CASCADE
);

-- TABLE message
CREATE TABLE message (
  id_message INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_groupe_message INTEGER UNSIGNED NOT NULL,
  id_auteur INTEGER UNSIGNED NOT NULL,
  contenu_message TEXT NOT NULL,
  date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  a_fichiers BOOLEAN DEFAULT FALSE,
  reply_to INTEGER UNSIGNED NULL, -- Ajouté pour la citation
  PRIMARY KEY(id_message),
  INDEX message_FKIndex1(id_groupe_message),
  INDEX message_FKIndex2(id_auteur),
  INDEX message_FKIndex3(reply_to),
  FOREIGN KEY(id_groupe_message) REFERENCES groupe_message(id_groupe_message) ON DELETE CASCADE,
  FOREIGN KEY(id_auteur) REFERENCES personne(id_personne) ON DELETE CASCADE,
  FOREIGN KEY(reply_to) REFERENCES message(id_message) ON DELETE SET NULL
);

-- TABLE visite (mise à jour pour référencer directement les invités)
CREATE TABLE visite (
  id_visite INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_invite INTEGER UNSIGNED NOT NULL, -- ID de l'invité
  email_visiteur VARCHAR(255) NULL, -- Email du visiteur (peut être différent de l'invité)
  motif_visite TEXT NULL,
  date_visite_start TIMESTAMP NOT NULL,
  date_visite_end TIMESTAMP NOT NULL,
  statut_visite ENUM('programmee', 'en_cours', 'terminee', 'annulee') DEFAULT 'programmee',
  PRIMARY KEY(id_visite),
  INDEX visite_FKIndex1(id_invite),
  FOREIGN KEY(id_invite) REFERENCES personne(id_personne) ON DELETE CASCADE
);

-- TABLE personal_access_tokens (pour Laravel Sanctum)
CREATE TABLE personal_access_tokens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tokenable_type VARCHAR(255) NOT NULL,
  tokenable_id VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  abilities TEXT NULL,
  last_used_at TIMESTAMP NULL,
  expires_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY(id),
  INDEX personal_access_tokens_tokenable_type_tokenable_id_index(tokenable_type, tokenable_id)
);

-- TABLE pour les réactions aux messages
CREATE TABLE message_reaction (
  id_reaction INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_message INTEGER UNSIGNED NOT NULL,
  id_personne INTEGER UNSIGNED NOT NULL, -- ID de la personne (résidente ou invitée)
  emoji VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  date_reaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_reaction),
  UNIQUE KEY unique_message_personne_emoji(id_message, id_personne, emoji),
  INDEX message_reaction_FKIndex1(id_message),
  INDEX message_reaction_FKIndex2(id_personne),
  FOREIGN KEY(id_message) REFERENCES message(id_message) ON DELETE CASCADE,
  FOREIGN KEY(id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- TABLE pour les fichiers partagés
CREATE TABLE message_fichier (
  id_fichier INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_message INTEGER UNSIGNED NOT NULL,
  nom_fichier VARCHAR(255) NOT NULL,
  nom_original VARCHAR(255) NOT NULL,
  chemin_fichier VARCHAR(500) NOT NULL,
  type_fichier VARCHAR(100) NOT NULL,
  taille_fichier INTEGER UNSIGNED NOT NULL,
  date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_fichier),
  INDEX message_fichier_FKIndex1(id_message),
  FOREIGN KEY(id_message) REFERENCES message(id_message) ON DELETE CASCADE
);

-- TABLE pour les invités (avec informations complémentaires)
CREATE TABLE invite (
  id_personne INTEGER UNSIGNED NOT NULL,
  actif BOOLEAN DEFAULT TRUE,
  date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_expiration TIMESTAMP NULL, -- Date d'expiration optionnelle
  invite_par INTEGER UNSIGNED NULL, -- ID de la personne qui a invité
  commentaire TEXT NULL, -- Commentaire sur l'invité
  PRIMARY KEY(id_personne),
  INDEX idx_actif (actif),
  INDEX idx_expiration (date_expiration),
  FOREIGN KEY(id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE,
  FOREIGN KEY(invite_par) REFERENCES personne(id_personne) ON DELETE SET NULL
);

-- TABLE logs (pour enregistrer les actions des utilisateurs)
CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL, -- ou email si tu ne veux pas de clé étrangère
  action VARCHAR(64) NOT NULL, -- ex: 'login', 'update_resident', 'send_message'
  message TEXT,                -- description humaine ou détails JSON
  ip_address VARCHAR(45) NULL, -- optionnel
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- TABLE incident (pour signaler des incidents)
CREATE TABLE incident (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  object TEXT NOT NULL,
  statut ENUM('a_venir', 'en_cours', 'resolu') NOT NULL DEFAULT 'en_cours',
  id_signaleur INT UNSIGNED NULL,
  pieces_jointes JSON NULL,
  PRIMARY KEY(id),
  INDEX idx_incident_datetime(datetime),
  INDEX idx_incident_statut(statut),
  FOREIGN KEY(id_signaleur) REFERENCES personne(id_personne) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- ----- ----- ----- ----- ----- ----- ----- ----- -----
-- Jeu de Test
-- ----- ----- ----- ----- ----- ----- ----- ----- -----

-- INSERTION DES PERSONNES

-- Administration
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('admin@residence.com', 'Dupont', 'Jean', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33123456789'),
('admin2@residence.com', 'Bernard', 'Sophie', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33123456790');

-- Gardiens
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('gardien@residence.com', 'Martin', 'Pierre', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33234567890'),
('gardien2@residence.com', 'Leroy', 'Antoine', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33234567891');

-- Résidents Bâtiment A
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('marie.durand@residence.com', 'Durand', 'Marie', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33345678901'),
('paul.moreau@residence.com', 'Moreau', 'Paul', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33456789012'),
('julie.petit@residence.com', 'Petit', 'Julie', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33567890123'),
('thomas.roux@residence.com', 'Roux', 'Thomas', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33678901234'),
('emma.blanc@residence.com', 'Blanc', 'Emma', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33789012345');

INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('lucas.noir@residence.com', 'Noir', 'Lucas', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33890123456'),
('lea.vert@residence.com', 'Vert', 'Lea', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33901234567'),
('hugo.rose@residence.com', 'Rose', 'Hugo', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33012345678'),
('chloe.orange@residence.com', 'Orange', 'Chloe', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33123456780'),
('maxime.violet@residence.com', 'Violet', 'Maxime', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33234567801');

-- Résidents Bâtiment B
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('sarah.bleu@residence.com', 'Bleu', 'Sarah', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33345678902'),
('kevin.jaune@residence.com', 'Jaune', 'Kevin', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33456789013'),
('manon.gris@residence.com', 'Gris', 'Manon', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33567890124'),
('antoine.rouge@residence.com', 'Rouge', 'Antoine', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33678901235'),
('clara.marron@residence.com', 'Marron', 'Clara', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33789012346');

INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('nathan.cyan@residence.com', 'Cyan', 'Nathan', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33890123457'),
('alice.dore@residence.com', 'Dore', 'Alice', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33901234568'),
('theo.argent@residence.com', 'Argente', 'Theo', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33012345679');

-- Résidents Bâtiment C
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('elise.bronze@residence.com', 'Bronze', 'Elise', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33123456781'),
('romain.cuivre@residence.com', 'Cuivre', 'Romain', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33234567802'),
('camille.platine@residence.com', 'Platine', 'Camille', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33345678903'),
('arthur.acier@residence.com', 'Acier', 'Arthur', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33456789014'),
('zoe.fer@residence.com', 'Fer', 'Zoe', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33567890125'),
('gabriel.plomb@residence.com', 'Plomb', 'Gabriel', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33678901236');

-- Insertion des administrateurs (utilisation de l'id_personne)
INSERT INTO admin (id_personne, niveau_acces) VALUES
(1, 'super_admin'),
(2, 'admin_standard');

-- Insertion des gardiens
INSERT INTO gardien (id_personne) VALUES
(3),
(4);

-- Insertion des résidents avec leurs adresses - Bâtiment A
INSERT INTO resident (id_personne, adresse_logement) VALUES
(5, 'Batiment A - Appartement 101'),
(6, 'Batiment A - Appartement 102'),
(7, 'Batiment A - Appartement 103'),
(8, 'Batiment A - Appartement 104'),
(9, 'Batiment A - Appartement 105');

INSERT INTO resident (id_personne, adresse_logement) VALUES
(10, 'Batiment A - Appartement 106'),
(11, 'Batiment A - Appartement 107'),
(12, 'Batiment A - Appartement 108'),
(13, 'Batiment A - Appartement 109'),
(14, 'Batiment A - Appartement 110');

-- Insertion des résidents avec leurs adresses - Bâtiment B
INSERT INTO resident (id_personne, adresse_logement) VALUES
(15, 'Batiment B - Appartement 201'),
(16, 'Batiment B - Appartement 202'),
(17, 'Batiment B - Appartement 203'),
(18, 'Batiment B - Appartement 204'),
(19, 'Batiment B - Appartement 205');

INSERT INTO resident (id_personne, adresse_logement) VALUES
(20, 'Batiment B - Appartement 206'),
(21, 'Batiment B - Appartement 207'),
(22, 'Batiment B - Appartement 208');

-- Insertion des résidents avec leurs adresses - Bâtiment C
INSERT INTO resident (id_personne, adresse_logement) VALUES
(23, 'Batiment C - Appartement 301'),
(24, 'Batiment C - Appartement 302'),
(25, 'Batiment C - Appartement 303'),
(26, 'Batiment C - Appartement 304'),
(27, 'Batiment C - Appartement 305'),
(28, 'Batiment C - Appartement 306');

-- Insertion des bans (logements) avec motifs
INSERT INTO ban (id_proprietaire, motif) VALUES
(6, 'Nuisances sonores répétées malgré avertissements'),
(5, NULL), -- Pas de motif spécifique renseigné
(15, 'Non-respect du règlement de copropriété'),
(23, 'Dégradations volontaires des parties communes');

-- Insertion des groupes de message
INSERT INTO groupe_message (nom_groupe, date_creation) VALUES
('Groupe General Residence', '2025-01-01 10:00:00'),
('Batiment A - Communication', '2025-01-02 09:00:00'),
('Batiment B - Communication', '2025-01-02 09:30:00'),
('Batiment C - Communication', '2025-01-02 10:00:00'),
('Gardiennage et Securite', '2025-01-03 08:00:00');

INSERT INTO groupe_message (nom_groupe, date_creation) VALUES
('Administration', '2025-01-03 08:30:00'),
('Evenements et Festivites', '2025-01-05 14:00:00'),
('Travaux et Maintenance', '2025-01-06 11:00:00'),
('Covoiturage', '2025-01-10 16:00:00'),
('Voisins Sportifs', '2025-01-12 18:00:00');

INSERT INTO groupe_message (nom_groupe, date_creation) VALUES
('Jardin Partage', '2025-01-15 10:30:00'),
('Parents de la Residence', '2025-01-18 14:30:00'),
('Discussion Jeunes', '2025-01-20 20:00:00'),
('Echanges de Services', '2025-01-22 12:00:00'),
('Conseil Syndical', '2025-01-25 15:00:00');

-- Insertion des liaisons personne-groupe - Groupe Général
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 1, '2025-06-30 14:30:00'),
(2, 1, '2025-06-30 14:25:00'),
(3, 1, '2025-06-30 13:45:00'),
(4, 1, '2025-06-30 13:40:00'),
(5, 1, '2025-06-30 12:15:00');

INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(6, 1, '2025-06-30 11:30:00'),
(7, 1, '2025-06-30 10:45:00'),
(8, 1, '2025-06-30 09:20:00'),
(9, 1, '2025-06-30 08:15:00'),
(10, 1, '2025-06-30 07:30:00');

INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(15, 1, '2025-06-30 16:45:00'),
(16, 1, '2025-06-30 15:20:00'),
(17, 1, '2025-06-30 14:10:00'),
(23, 1, '2025-06-30 13:25:00'),
(24, 1, '2025-06-30 12:40:00');

-- Bâtiment A - Communication
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 2, '2025-06-30 14:25:00'),
(5, 2, '2025-06-30 12:20:00'),
(6, 2, '2025-06-30 11:35:00'),
(7, 2, '2025-06-30 10:50:00'),
(8, 2, '2025-06-30 09:25:00');

INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(9, 2, '2025-06-30 08:20:00'),
(10, 2, '2025-06-30 07:35:00'),
(11, 2, '2025-06-30 18:10:00'),
(12, 2, '2025-06-30 17:25:00'),
(13, 2, '2025-06-30 16:40:00');

INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(14, 2, '2025-06-30 15:55:00');

-- Bâtiment B - Communication
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 3, '2025-06-30 14:20:00'),
(15, 3, '2025-06-30 16:50:00'),
(16, 3, '2025-06-30 15:25:00'),
(17, 3, '2025-06-30 14:15:00'),
(18, 3, '2025-06-30 13:30:00');

INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(19, 3, '2025-06-30 12:45:00'),
(20, 3, '2025-06-30 11:50:00'),
(21, 3, '2025-06-30 10:35:00'),
(22, 3, '2025-06-30 09:40:00');

-- Bâtiment C - Communication
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 4, '2025-06-30 14:15:00'),
(23, 4, '2025-06-30 13:30:00'),
(24, 4, '2025-06-30 12:45:00'),
(25, 4, '2025-06-30 11:50:00'),
(26, 4, '2025-06-30 10:55:00');

INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(27, 4, '2025-06-30 09:40:00'),
(28, 4, '2025-06-30 08:25:00');

-- Gardiennage et Sécurité
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 5, '2025-06-30 14:20:00'),
(2, 5, '2025-06-30 14:15:00'),
(3, 5, '2025-06-30 13:50:00'),
(4, 5, '2025-06-30 13:45:00');

-- Administration
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 6, '2025-06-30 14:35:00'),
(2, 6, '2025-06-30 14:30:00'),
(3, 6, '2025-06-30 09:30:00');

-- Événements et Festivités (Groupe 7)
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(1, 7, '2025-06-30 14:10:00'),
(5, 7, '2025-06-30 12:30:00'),
(7, 7, '2025-06-30 11:45:00'),
(15, 7, '2025-06-30 16:20:00'),
(17, 7, '2025-06-30 13:15:00'),
(23, 7, '2025-06-30 10:45:00'),
(25, 7, '2025-06-30 09:30:00'),
(21, 7, '2025-06-30 08:15:00');

-- Messages du Groupe General Residence

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 1, 'Bienvenue dans le systeme de gestion de la residence ! Nhesitez pas a utiliser cette plateforme pour communiquer.', '2025-06-25 09:00:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 3, 'Bonjour a tous ! Je suis Pierre, votre gardien. Je suis disponible 24h/24 pour toute urgence au 0234567890.', '2025-06-25 09:15:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 5, 'Merci pour cette initiative ! Cest tres pratique davoir un systeme de communication commun.', '2025-06-25 10:30:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 6, 'Excellente idee ! Enfin on va pouvoir se coordonner plus facilement.', '2025-06-25 11:45:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 1, 'Rappel : les poubelles doivent etre sorties avant 7h les mardis et vendredis.', '2025-06-26 08:00:00');

-- Exemple de message avec citation (reply_to)
-- Julie répond à Pierre (message 2)
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi, reply_to) VALUES
(1, 7, 'Merci Pierre pour ta disponibilité !', '2025-06-26 09:00:00', 2);

-- Paul répond à Julie (message 6)
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi, reply_to) VALUES
(1, 6, 'Bienvenue Julie !', '2025-06-26 09:30:00', 6);


INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 7, 'Y a-t-il une possibilite dorganiser une reunion residents bientot ?', '2025-06-26 14:20:00'),
(1, 1, 'Bonne idee Julie ! Nous organiserons une reunion generale le 15 juillet a 18h en salle commune.', '2025-06-26 15:30:00'),
(1, 8, 'Parfait, je noterai dans mon agenda. Merci !', '2025-06-26 16:15:00'),
(1, 3, 'Information importante : travaux de maintenance de lascenseur batiment B prevu demain de 9h a 12h.', '2025-06-27 18:00:00'),
(1, 15, 'Merci pour linfo Pierre ! Je prendrai les escaliers.', '2025-06-27 18:30:00');

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(1, 9, 'Bonsoir tout le monde ! Nouvelle residente du A-105, ravie de vous rencontrer virtuellement !', '2025-06-28 19:00:00'),
(1, 5, 'Bienvenue Emma ! Si tu as besoin de quoi que ce soit, nhesites pas.', '2025-06-28 19:15:00'),
(1, 1, 'Rappel : la fete des voisins aura lieu samedi prochain dans le jardin. Venez nombreux !', '2025-06-29 10:00:00'),
(1, 10, 'Super ! Japporterai ma guitare pour lambiance', '2025-06-29 10:30:00'),
(1, 17, 'Genial ! Je preparerai des gateaux maison', '2025-06-29 11:00:00');

-- Messages Evenements et Festivites

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 1, 'Qui a des idees pour la fete des voisins de samedi ?', '2025-06-25 15:00:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 5, 'On pourrait organiser un barbecue collectif ! Jai un grand barbecue.', '2025-06-25 15:30:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 7, 'Excellente idee ! Je peux apporter des salades.', '2025-06-25 16:00:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 15, 'Et moi des boissons ! Ca va etre genial !', '2025-06-25 16:15:00');
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 17, 'Je moccupe des desserts ! Jadore patisser', '2025-06-25 17:00:00');

-- Exemple de message avec citation (reply_to)
-- Elise répond à l'idée de barbecue de Paul (message 12)
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi, reply_to) VALUES
(7, 23, 'Super idée Paul, je peux apporter des jeux pour les enfants !', '2025-06-25 17:30:00', 12);

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 23, 'Super ! Et si on organisait aussi des jeux pour les enfants ?', '2025-06-26 10:00:00'),
(7, 25, 'Bonne idee Elise ! Jai des jeux de societe quon peut sortir.', '2025-06-26 10:30:00'),
(7, 21, 'Il faut quon verifie la meteo !', '2025-06-26 11:00:00'),
(7, 1, 'Meteo annoncee : ensoleille ! Tout est parfait !', '2025-06-26 18:00:00');

-- Messages Travaux et Maintenance
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(8, 1, 'Planning des travaux de la semaine : Lundi - peinture hall B, Mercredi - jardinage, Vendredi - nettoyage parkings.', '2025-06-24 08:00:00'),
(8, 3, 'Recu. Je coordonnerai avec les equipes. Lacces sera maintenu partout.', '2025-06-24 08:30:00'),
(8, 6, 'Y a-t-il possibilite de repeindre aussi les boites aux lettres ? Elles commencent a rouiller.', '2025-06-25 09:00:00'),
(8, 1, 'Bonne remarque Paul ! Je lajoute a la liste des travaux du mois prochain.', '2025-06-25 10:00:00'),
(8, 8, 'La porte dentree du batiment A grince beaucoup, ca pourrait etre huile ?', '2025-06-26 12:00:00');

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(8, 3, 'Je men occupe cet apres-midi Thomas !', '2025-06-26 12:15:00'),
(8, 16, 'Les ampoules du parking B sont grillees, cest assez sombre le soir.', '2025-06-27 19:00:00'),
(8, 1, 'Note Kevin, remplacement prevu demain matin par lelectricien.', '2025-06-27 19:30:00'),
(8, 24, 'Merci pour votre reactivite ! Cest appreciable davoir une equipe efficace.', '2025-06-27 20:00:00');

-- Messages Covoiturage
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(9, 5, 'Salut ! Qui va au centre-ville demain matin vers 9h ? Je peux prendre 2 personnes', '2025-06-28 20:00:00'),
(9, 8, 'Moi ! Jai RDV chez le dentiste a 9h30, ca marrangerait !', '2025-06-28 20:15:00'),
(9, 16, 'Super Marie ! Moi aussi je veux bien, jai des courses a faire.', '2025-06-28 20:30:00'),
(9, 5, 'Parfait ! RDV 8h45 devant lentree principale !', '2025-06-28 20:45:00'),
(9, 18, 'Quelquun va a laeroport vendredi ? Je peux participer aux frais !', '2025-06-29 14:00:00');

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(9, 26, 'Desole Antoine, moi cest samedi. Regarde sur lapp BlaBlaCar ?', '2025-06-29 14:30:00'),
(9, 28, 'Moi jy vais vendredi Antoine ! A quelle heure ?', '2025-06-29 15:00:00'),
(9, 18, 'Genial Gabriel ! Vol a 16h, donc depart vers 13h30 ?', '2025-06-29 15:15:00'),
(9, 28, 'Parfait ! MP moi pour les details', '2025-06-29 15:30:00');

-- Messages Voisins Sportifs
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(10, 6, 'Salut les sportifs ! Qui est motive pour un jogging demain matin ?', '2025-06-28 18:00:00'),
(10, 10, 'Moi ! Quelle heure et quel parcours ?', '2025-06-28 18:15:00'),
(10, 12, 'Count me in ! Jai besoin de me remettre en forme !', '2025-06-28 18:30:00'),
(10, 6, '7h devant la residence, on fait le tour du parc (5km environ) ?', '2025-06-28 18:45:00'),
(10, 20, 'Top ! Moi qui cherchais un groupe de running !', '2025-06-28 19:00:00');

INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(10, 22, 'Ah zut, je peux pas demain mais la prochaine fois !', '2025-06-28 19:15:00'),
(10, 26, 'Et si on organisait aussi des sessions tennis ? Il y a des courts pres dici', '2025-06-29 16:00:00'),
(10, 10, 'Excellente idee Arthur ! Je suis joueur regulier, on peut faire des doubles !', '2025-06-29 16:30:00'),
(10, 6, 'Super ! On pourrait faire jogging le matin, tennis lapres-midi ?', '2025-06-29 17:00:00');

-- Insertion d'invités (avec mot de passe)
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('visiteur1@email.com', 'Dupont', 'Pierre', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33612345678'),
('visiteur2@email.com', 'Martin', 'Sophie', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+33698765432'),
('famille.invitee@email.com', 'Famille', 'Invitée', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+228656781234'),
('invite.temp@email.com', 'Temporaire', 'Invité', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+228612987654'),
('ami.resident@email.com', 'Ami', 'Résident', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+228687654321'),
('visiteur.weekend@email.com', 'Weekend', 'Visiteur', '$2y$10$FycjpWZZ3NoBalGyU3WlD.CBL4Bez.t6wHdsN25fUoOfTOlm.SmRq', '+228643218765');

-- Marquage de ces personnes comme invités (avec informations complémentaires)
INSERT INTO invite (id_personne, actif, invite_par, commentaire) VALUES
(29, TRUE, 5, 'Ami proche, visite régulière'),
(30, TRUE, 5, 'Visite amicale temporaire'),
(31, TRUE, 6, 'Famille en visite weekend'),
(32, TRUE, NULL, 'Invité temporaire sans parrain spécifique'),
(33, TRUE, 8, 'Ami découvrant la résidence'),
(34, TRUE, 15, 'Visiteur événements weekend');

-- Insertion des visites liées aux invités
INSERT INTO visite (id_invite, email_visiteur, motif_visite, statut_visite, date_visite_start, date_visite_end) VALUES
(29, 'visiteur1@email.com', 'Visite de courtoisie - participation événements', 'terminee', '2025-06-25 10:00:00', '2025-06-25 18:00:00'),
(30, 'visiteur2@email.com', 'Visite amicale - séjour temporaire', 'terminee', '2025-06-26 14:00:00', '2025-06-28 16:00:00'),
(31, 'famille.invitee@email.com', 'Visite familiale weekend', 'programmee', '2025-07-01 10:00:00', '2025-07-03 18:00:00'),
(33, 'ami.resident@email.com', 'Visite d ami - découverte résidence', 'en_cours', '2025-07-01 09:00:00', '2025-07-02 20:00:00'),
(34, 'visiteur.weekend@email.com', 'Visite weekend - événements', 'programmee', '2025-07-05 16:00:00', '2025-07-07 12:00:00'),
(29, 'visiteur1@email.com', 'Seconde visite - reunion résidents', 'programmee', '2025-07-15 18:00:00', '2025-07-15 21:00:00');

-- Ajout des invités aux groupes de message (via personne_groupe)
INSERT INTO personne_groupe (id_personne, id_groupe_message, derniere_connexion) VALUES
(29, 1, '2025-06-30 10:00:00'), -- Groupe général
(29, 7, '2025-06-30 10:00:00'), -- Événements et festivités
(30, 1, '2025-06-30 15:30:00'), -- Groupe général
(30, 7, '2025-06-30 15:30:00'), -- Événements et festivités
(31, 1, '2025-06-30 18:45:00'), -- Groupe général
(31, 2, '2025-06-30 18:45:00'), -- Bâtiment A
(33, 1, '2025-07-01 09:00:00'), -- Groupe général
(33, 7, '2025-07-01 09:00:00'), -- Événements et festivités
(34, 1, '2025-07-01 16:00:00'), -- Groupe général
(34, 7, '2025-07-01 16:00:00'); -- Événements et festivités

-- Messages d'exemple envoyés par des invités
INSERT INTO message (id_groupe_message, id_auteur, contenu_message, date_envoi) VALUES
(7, 29, 'Merci beaucoup pour l invitation à la fête des voisins ! J ai hâte de vous rencontrer tous !', '2025-06-30 10:00:00'),
(1, 30, 'Bonjour tout le monde ! Je suis Sophie, amie de Marie. Merci de m inclure dans vos échanges pendant ma visite.', '2025-06-30 15:30:00'),
(1, 31, 'Bonsoir, nous sommes la famille invitée par Paul. Nous serons là ce weekend, au plaisir de vous rencontrer !', '2025-06-30 18:45:00'),
(7, 34, 'Bonjour ! J aimerais participer aux événements du weekend si c est possible !', '2025-07-01 16:30:00'),
(1, 5, 'Bienvenue à tous nos invités ! N hésitez pas à poser des questions si vous en avez.', '2025-07-01 17:00:00');

-- Ajout des invités temporaires au groupe Événements et Festivités pour qu'ils puissent recevoir les messages
-- SUPPRESSION DE CETTE SECTION - DOUBLONS DÉTECTÉS
-- Les personnes suivantes sont déjà assignées au groupe 7 via d'autres insertions ou doivent être ajoutées dans la section appropriée
-- Cette section créait des doublons avec les clés uniques (email_personne, id_groupe_message)

-- Exemples de réactions aux messages avec des emojis Unicode
INSERT INTO message_reaction (id_message, id_personne, emoji, date_reaction) VALUES
-- Réactions au message de bienvenue (message 1)
(1, 5, '👍', '2025-06-25 09:05:00'),
(1, 6, '❤️', '2025-06-25 09:10:00'),
(1, 7, '👏', '2025-06-25 09:15:00'),
(1, 15, '😊', '2025-06-25 09:20:00'),

-- Réactions au message du gardien (message 2)
(2, 1, '👍', '2025-06-25 09:20:00'),
(2, 5, '💪', '2025-06-25 09:25:00'),
(2, 8, '🙏', '2025-06-25 09:30:00'),

-- Réactions au message sur la fête des voisins (message 13)
(13, 5, '🎉', '2025-06-29 10:05:00'),
(13, 7, '🎵', '2025-06-29 10:10:00'),
(13, 15, '🍰', '2025-06-29 10:15:00'),
(13, 17, '👨‍🍳', '2025-06-29 10:20:00'),
(13, 10, '🎸', '2025-06-29 10:35:00'),

-- Réactions aux messages événements
(20, 1, '☀️', '2025-06-26 18:05:00'),
(20, 5, '🎉', '2025-06-26 18:10:00'),
(20, 7, '🥳', '2025-06-26 18:15:00');



-- INSERTION DES INCIDENTS INITIAUX (avec et sans pièces jointes)
INSERT INTO incident (datetime, object, statut, id_signaleur, pieces_jointes) VALUES
('2025-06-15 08:30:00', 'Fuite d’eau importante au 2e étage, cage A', 'en_cours', 5, '["/storage/incidents/photo_fuite.jpg"]'),
('2025-06-18 19:00:00', 'Porte d’entrée principale cassée, accès difficile', 'resolu', 3, NULL),
('2025-06-20 14:45:00', 'Ascenseur en panne, bâtiment B', 'en_cours', 16, '["/storage/incidents/ascenseur_panne.pdf"]'),
('2025-06-22 10:10:00', 'Dégradation des boîtes aux lettres, graffiti', 'a_venir', 7, NULL),
('2025-06-25 17:20:00', 'Odeur suspecte dans le parking sous-sol', 'en_cours', 8, NULL),
('2025-06-28 21:00:00', 'Tapage nocturne récurrent, appartement 102', 'resolu', 6, '["/storage/incidents/audio_tapage.mp3"]'),
('2025-07-01 09:30:00', 'Fenêtre brisée dans la salle commune', 'en_cours', 23, NULL),
('2025-07-03 15:00:00', 'Problème d’éclairage dans le hall B', 'resolu', 15, NULL),
('2025-07-05 11:15:00', 'Débordement des poubelles extérieures', 'en_cours', 9, '["/storage/incidents/photo_poubelles.jpg","/storage/incidents/video_poubelles.mp4"]'),
('2025-07-08 13:40:00', 'Vélo abandonné dans le local technique', 'a_venir', 12, NULL);



-- ===============================================
-- AJOUT D'INDEX POUR OPTIMISATION DES PERFORMANCES
-- ===============================================
INSERT INTO message_fichier (id_message, nom_fichier, nom_original, chemin_fichier, type_fichier, taille_fichier, date_upload) VALUES
(5, 'reglement_poubelles_20250626.pdf', 'Règlement Poubelles.pdf', '/uploads/documents/reglement_poubelles_20250626.pdf', 'application/pdf', 245760, '2025-06-26 08:05:00'),
(9, 'planning_maintenance_juillet.xlsx', 'Planning Maintenance Juillet.xlsx', '/uploads/documents/planning_maintenance_juillet.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 128540, '2025-06-27 17:55:00');

-- Mettre à jour les messages qui ont des fichiers
UPDATE message SET a_fichiers = TRUE WHERE id_message IN (5, 9);

-- ===============================================
-- AJOUT D'INDEX POUR OPTIMISATION DES PERFORMANCES
-- ===============================================

-- Index composites pour les requêtes fréquentes
CREATE INDEX idx_message_groupe_date ON message(id_groupe_message, date_envoi DESC);
CREATE INDEX idx_message_auteur_date ON message(id_auteur, date_envoi DESC);

-- Index pour les statistiques utilisateur
CREATE INDEX idx_personne_groupe_connexion ON personne_groupe(id_personne, derniere_connexion DESC);

-- Index pour les recherches de messages (avec longueur spécifiée pour TEXT)
CREATE INDEX idx_message_contenu_search ON message(contenu_message(255));

-- Index FULLTEXT pour la recherche textuelle avancée (optionnel)
-- ALTER TABLE message ADD FULLTEXT(contenu_message);

-- Index pour les visites
CREATE INDEX idx_visite_statut_date ON visite(statut_visite, date_visite_start);
CREATE INDEX idx_visite_invite_date ON visite(id_invite, date_visite_start DESC);


