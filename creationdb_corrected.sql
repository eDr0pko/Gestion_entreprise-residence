-- Gestion d'entreprise de résidence

-- Creation de la base de données
CREATE DATABASE IF NOT EXISTS gestion_entreprise_residence;
USE gestion_entreprise_residence;
DROP TABLE IF EXISTS personne, admin, gardien, resident, ban, groupe_message, personne_groupe, message, invite, visite, personal_access_tokens, message_statut, message_reaction, message_fichier;


-- TABLE personne
CREATE TABLE personne (
  email VARCHAR(255) NOT NULL,
  nom VARCHAR(45) NOT NULL,
  prenom VARCHAR(45) NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  numero_telephone VARCHAR(20) NOT NULL,
  PRIMARY KEY(email)
);

-- TABLE admin
CREATE TABLE admin (
  id_admin INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email_personne VARCHAR(255) NOT NULL,
  mot_de_passe_admin VARCHAR(255) NOT NULL,
  PRIMARY KEY(id_admin),
  UNIQUE KEY(email_personne),
  FOREIGN KEY(email_personne) REFERENCES personne(email) ON DELETE CASCADE
);

-- TABLE gardien
CREATE TABLE gardien (
  id_gardien INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email_personne VARCHAR(255) NOT NULL,
  PRIMARY KEY(id_gardien),
  INDEX gardien_FKIndex1(email_personne),
  FOREIGN KEY(email_personne) REFERENCES personne(email) ON DELETE CASCADE
);

-- TABLE resident
CREATE TABLE resident (
  id_resident INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email_personne VARCHAR(255) NOT NULL,
  adresse_logement VARCHAR(255) NOT NULL,
  PRIMARY KEY(id_resident),
  INDEX resident_FKIndex1(email_personne),
  FOREIGN KEY(email_personne) REFERENCES personne(email) ON DELETE CASCADE
);

-- TABLE ban (appartement/logement)
CREATE TABLE ban (
  id_ban INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email_proprietaire VARCHAR(255) NOT NULL,
  PRIMARY KEY(id_ban),
  INDEX ban_FKIndex1(email_proprietaire),
  FOREIGN KEY(email_proprietaire) REFERENCES personne(email) ON DELETE CASCADE
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
  email_personne VARCHAR(255) NOT NULL,
  id_groupe_message INTEGER UNSIGNED NOT NULL,
  date_adhesion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  derniere_connexion TIMESTAMP NULL,
  PRIMARY KEY(id_personne_groupe),
  UNIQUE KEY unique_personne_groupe(email_personne, id_groupe_message),
  INDEX personne_groupe_FKIndex1(email_personne),
  INDEX personne_groupe_FKIndex2(id_groupe_message),
  FOREIGN KEY(email_personne) REFERENCES personne(email) ON DELETE CASCADE,
  FOREIGN KEY(id_groupe_message) REFERENCES groupe_message(id_groupe_message) ON DELETE CASCADE
);

-- TABLE message
CREATE TABLE message (
  id_message INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_groupe_message INTEGER UNSIGNED NOT NULL,
  email_auteur VARCHAR(255) NOT NULL,
  contenu_message TEXT NOT NULL,
  date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_message),
  INDEX message_FKIndex1(id_groupe_message),
  INDEX message_FKIndex2(email_auteur),
  FOREIGN KEY(id_groupe_message) REFERENCES groupe_message(id_groupe_message) ON DELETE CASCADE,
  FOREIGN KEY(email_auteur) REFERENCES personne(email) ON DELETE CASCADE
);

-- TABLE invite (invitation)
CREATE TABLE invite (
  id_invitation INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email_invite VARCHAR(255) NOT NULL,
  id_ban INTEGER UNSIGNED NOT NULL,
  photo_invite VARCHAR(255) NULL,
  date_invitation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut_invitation ENUM('en_attente', 'acceptee', 'refusee') DEFAULT 'en_attente',
  PRIMARY KEY(id_invitation),
  INDEX Invite_FKIndex1(email_invite),
  INDEX Invite_FKIndex2(id_ban),
  FOREIGN KEY(email_invite) REFERENCES personne(email) ON DELETE CASCADE,
  FOREIGN KEY(id_ban) REFERENCES ban(id_ban) ON DELETE CASCADE
);

-- TABLE visite
CREATE TABLE visite (
  id_visite INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  email_visiteur VARCHAR(255) NOT NULL,
  id_invitation INTEGER UNSIGNED NOT NULL,
  motif_visite TEXT NULL,
  date_visite_start TIMESTAMP NOT NULL,
  date_visite_end TIMESTAMP NOT NULL,
  statut_visite ENUM('programmee', 'en_cours', 'terminee', 'annulee') DEFAULT 'programmee',
  PRIMARY KEY(id_visite),
  INDEX visite_FKIndex1(email_visiteur),
  INDEX visite_FKIndex2(id_invitation),
  FOREIGN KEY(email_visiteur) REFERENCES personne(email) ON DELETE CASCADE,
  FOREIGN KEY(id_invitation) REFERENCES invite(id_invitation) ON DELETE CASCADE
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

-- TABLE pour les statuts de lecture avancés
CREATE TABLE message_statut (
  id_statut INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_message INTEGER UNSIGNED NOT NULL,
  email_personne VARCHAR(255) NOT NULL,
  statut ENUM('envoye', 'recu', 'lu') DEFAULT 'recu',
  date_statut TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_statut),
  UNIQUE KEY unique_message_personne(id_message, email_personne),
  INDEX message_statut_FKIndex1(id_message),
  INDEX message_statut_FKIndex2(email_personne),
  FOREIGN KEY(id_message) REFERENCES message(id_message) ON DELETE CASCADE,
  FOREIGN KEY(email_personne) REFERENCES personne(email) ON DELETE CASCADE
);

-- TABLE pour les réactions aux messages
CREATE TABLE message_reaction (
  id_reaction INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_message INTEGER UNSIGNED NOT NULL,
  email_personne VARCHAR(255) NOT NULL,
  emoji VARCHAR(10) NOT NULL,
  date_reaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_reaction),
  UNIQUE KEY unique_message_personne_emoji(id_message, email_personne, emoji),
  INDEX message_reaction_FKIndex1(id_message),
  INDEX message_reaction_FKIndex2(email_personne),
  FOREIGN KEY(id_message) REFERENCES message(id_message) ON DELETE CASCADE,
  FOREIGN KEY(email_personne) REFERENCES personne(email) ON DELETE CASCADE
);

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

-- Ajouter une colonne pour indiquer si un message a des fichiers
ALTER TABLE message ADD COLUMN a_fichiers BOOLEAN DEFAULT FALSE;


-- INSERTION DES PERSONNES

-- Administration
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('admin@residence.com', 'Dupont', 'Jean', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0123456789'),
('admin2@residence.com', 'Bernard', 'Sophie', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0123456790');

-- Gardiens
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('gardien@residence.com', 'Martin', 'Pierre', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0234567890'),
('gardien2@residence.com', 'Leroy', 'Antoine', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0234567891');

-- Résidents Bâtiment A
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('marie.durand@residence.com', 'Durand', 'Marie', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0345678901'),
('paul.moreau@residence.com', 'Moreau', 'Paul', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0456789012'),
('julie.petit@residence.com', 'Petit', 'Julie', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0567890123'),
('thomas.roux@residence.com', 'Roux', 'Thomas', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0678901234'),
('emma.blanc@residence.com', 'Blanc', 'Emma', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0789012345');

INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('lucas.noir@residence.com', 'Noir', 'Lucas', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0890123456'),
('lea.vert@residence.com', 'Vert', 'Lea', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0901234567'),
('hugo.rose@residence.com', 'Rose', 'Hugo', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0012345678'),
('chloe.orange@residence.com', 'Orange', 'Chloe', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0123456780'),
('maxime.violet@residence.com', 'Violet', 'Maxime', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0234567801');

-- Résidents Bâtiment B
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('sarah.bleu@residence.com', 'Bleu', 'Sarah', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0345678902'),
('kevin.jaune@residence.com', 'Jaune', 'Kevin', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0456789013'),
('manon.gris@residence.com', 'Gris', 'Manon', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0567890124'),
('antoine.rouge@residence.com', 'Rouge', 'Antoine', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0678901235'),
('clara.marron@residence.com', 'Marron', 'Clara', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0789012346');

INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('nathan.cyan@residence.com', 'Cyan', 'Nathan', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0890123457'),
('alice.dore@residence.com', 'Dore', 'Alice', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0901234568'),
('theo.argent@residence.com', 'Argente', 'Theo', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0012345679');

-- Résidents Bâtiment C
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('elise.bronze@residence.com', 'Bronze', 'Elise', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0123456781'),
('romain.cuivre@residence.com', 'Cuivre', 'Romain', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0234567802'),
('camille.platine@residence.com', 'Platine', 'Camille', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0345678903'),
('arthur.acier@residence.com', 'Acier', 'Arthur', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0456789014'),
('zoe.fer@residence.com', 'Fer', 'Zoe', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0567890125'),
('gabriel.plomb@residence.com', 'Plomb', 'Gabriel', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC', '0678901236');

-- Insertion des administrateurs
INSERT INTO admin (email_personne, mot_de_passe_admin) VALUES
('admin@residence.com', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC'),
('admin2@residence.com', '$2y$10$YU6KtsC8YI8Lc4kJNhyTk.gMhVpdk7nfvKQhTWlJ0HToe.m5mddkC');

-- Insertion des gardiens
INSERT INTO gardien (email_personne) VALUES
('gardien@residence.com'),
('gardien2@residence.com');

-- Insertion des résidents avec leurs adresses - Bâtiment A
INSERT INTO resident (email_personne, adresse_logement) VALUES
('marie.durand@residence.com', 'Batiment A - Appartement 101'),
('paul.moreau@residence.com', 'Batiment A - Appartement 102'),
('julie.petit@residence.com', 'Batiment A - Appartement 103'),
('thomas.roux@residence.com', 'Batiment A - Appartement 104'),
('emma.blanc@residence.com', 'Batiment A - Appartement 105');

INSERT INTO resident (email_personne, adresse_logement) VALUES
('lucas.noir@residence.com', 'Batiment A - Appartement 106'),
('lea.vert@residence.com', 'Batiment A - Appartement 107'),
('hugo.rose@residence.com', 'Batiment A - Appartement 108'),
('chloe.orange@residence.com', 'Batiment A - Appartement 109'),
('maxime.violet@residence.com', 'Batiment A - Appartement 110');

-- Insertion des résidents avec leurs adresses - Bâtiment B
INSERT INTO resident (email_personne, adresse_logement) VALUES
('sarah.bleu@residence.com', 'Batiment B - Appartement 201'),
('kevin.jaune@residence.com', 'Batiment B - Appartement 202'),
('manon.gris@residence.com', 'Batiment B - Appartement 203'),
('antoine.rouge@residence.com', 'Batiment B - Appartement 204'),
('clara.marron@residence.com', 'Batiment B - Appartement 205');

INSERT INTO resident (email_personne, adresse_logement) VALUES
('nathan.cyan@residence.com', 'Batiment B - Appartement 206'),
('alice.dore@residence.com', 'Batiment B - Appartement 207'),
('theo.argent@residence.com', 'Batiment B - Appartement 208');

-- Insertion des résidents avec leurs adresses - Bâtiment C
INSERT INTO resident (email_personne, adresse_logement) VALUES
('elise.bronze@residence.com', 'Batiment C - Appartement 301'),
('romain.cuivre@residence.com', 'Batiment C - Appartement 302'),
('camille.platine@residence.com', 'Batiment C - Appartement 303'),
('arthur.acier@residence.com', 'Batiment C - Appartement 304'),
('zoe.fer@residence.com', 'Batiment C - Appartement 305'),
('gabriel.plomb@residence.com', 'Batiment C - Appartement 306');

-- Insertion des bans (logements)
INSERT INTO ban (email_proprietaire) VALUES
('paul.moreau@residence.com');

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
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('admin@residence.com', 1, '2025-06-30 14:30:00'),
('admin2@residence.com', 1, '2025-06-30 14:25:00'),
('gardien@residence.com', 1, '2025-06-30 13:45:00'),
('gardien2@residence.com', 1, '2025-06-30 13:40:00'),
('marie.durand@residence.com', 1, '2025-06-30 12:15:00');

INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('paul.moreau@residence.com', 1, '2025-06-30 11:30:00'),
('julie.petit@residence.com', 1, '2025-06-30 10:45:00'),
('thomas.roux@residence.com', 1, '2025-06-30 09:20:00'),
('emma.blanc@residence.com', 1, '2025-06-30 08:15:00'),
('lucas.noir@residence.com', 1, '2025-06-30 07:30:00');

INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('sarah.bleu@residence.com', 1, '2025-06-30 16:45:00'),
('kevin.jaune@residence.com', 1, '2025-06-30 15:20:00'),
('manon.gris@residence.com', 1, '2025-06-30 14:10:00'),
('elise.bronze@residence.com', 1, '2025-06-30 13:25:00'),
('romain.cuivre@residence.com', 1, '2025-06-30 12:40:00');

-- Bâtiment A - Communication
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('admin@residence.com', 2, '2025-06-30 14:25:00'),
('marie.durand@residence.com', 2, '2025-06-30 12:20:00'),
('paul.moreau@residence.com', 2, '2025-06-30 11:35:00'),
('julie.petit@residence.com', 2, '2025-06-30 10:50:00'),
('thomas.roux@residence.com', 2, '2025-06-30 09:25:00');

INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('emma.blanc@residence.com', 2, '2025-06-30 08:20:00'),
('lucas.noir@residence.com', 2, '2025-06-30 07:35:00'),
('lea.vert@residence.com', 2, '2025-06-30 18:10:00'),
('hugo.rose@residence.com', 2, '2025-06-30 17:25:00'),
('chloe.orange@residence.com', 2, '2025-06-30 16:40:00');

INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('maxime.violet@residence.com', 2, '2025-06-30 15:55:00');

-- Bâtiment B - Communication
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('admin@residence.com', 3, '2025-06-30 14:20:00'),
('sarah.bleu@residence.com', 3, '2025-06-30 16:50:00'),
('kevin.jaune@residence.com', 3, '2025-06-30 15:25:00'),
('manon.gris@residence.com', 3, '2025-06-30 14:15:00'),
('antoine.rouge@residence.com', 3, '2025-06-30 13:30:00');

INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('clara.marron@residence.com', 3, '2025-06-30 12:45:00'),
('nathan.cyan@residence.com', 3, '2025-06-30 11:50:00'),
('alice.dore@residence.com', 3, '2025-06-30 10:35:00'),
('theo.argent@residence.com', 3, '2025-06-30 09:40:00');

-- Bâtiment C - Communication
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('admin@residence.com', 4, '2025-06-30 14:15:00'),
('elise.bronze@residence.com', 4, '2025-06-30 13:30:00'),
('romain.cuivre@residence.com', 4, '2025-06-30 12:45:00'),
('camille.platine@residence.com', 4, '2025-06-30 11:50:00'),
('arthur.acier@residence.com', 4, '2025-06-30 10:55:00');

INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('zoe.fer@residence.com', 4, '2025-06-30 09:40:00'),
('gabriel.plomb@residence.com', 4, '2025-06-30 08:25:00');

-- Gardiennage et Sécurité
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('admin@residence.com', 5, '2025-06-30 14:20:00'),
('admin2@residence.com', 5, '2025-06-30 14:15:00'),
('gardien@residence.com', 5, '2025-06-30 13:50:00'),
('gardien2@residence.com', 5, '2025-06-30 13:45:00');

-- Administration
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
('admin@residence.com', 6, '2025-06-30 14:35:00'),
('admin2@residence.com', 6, '2025-06-30 14:30:00'),
('gardien@residence.com', 6, '2025-06-30 09:30:00');

-- Messages du Groupe General Residence
INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(1, 'admin@residence.com', 'Bienvenue dans le systeme de gestion de la residence ! Nhesitez pas a utiliser cette plateforme pour communiquer.', '2025-06-25 09:00:00'),
(1, 'gardien@residence.com', 'Bonjour a tous ! Je suis Pierre, votre gardien. Je suis disponible 24h/24 pour toute urgence au 0234567890.', '2025-06-25 09:15:00'),
(1, 'marie.durand@residence.com', 'Merci pour cette initiative ! Cest tres pratique davoir un systeme de communication commun.', '2025-06-25 10:30:00'),
(1, 'paul.moreau@residence.com', 'Excellente idee ! Enfin on va pouvoir se coordonner plus facilement.', '2025-06-25 11:45:00'),
(1, 'admin@residence.com', 'Rappel : les poubelles doivent etre sorties avant 7h les mardis et vendredis.', '2025-06-26 08:00:00');

INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(1, 'julie.petit@residence.com', 'Y a-t-il une possibilite dorganiser une reunion residents bientot ?', '2025-06-26 14:20:00'),
(1, 'admin@residence.com', 'Bonne idee Julie ! Nous organiserons une reunion generale le 15 juillet a 18h en salle commune.', '2025-06-26 15:30:00'),
(1, 'thomas.roux@residence.com', 'Parfait, je noterai dans mon agenda. Merci !', '2025-06-26 16:15:00'),
(1, 'gardien@residence.com', 'Information importante : travaux de maintenance de lascenseur batiment B prevu demain de 9h a 12h.', '2025-06-27 18:00:00'),
(1, 'sarah.bleu@residence.com', 'Merci pour linfo Pierre ! Je prendrai les escaliers.', '2025-06-27 18:30:00');

INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(1, 'emma.blanc@residence.com', 'Bonsoir tout le monde ! Nouvelle residente du A-105, ravie de vous rencontrer virtuellement !', '2025-06-28 19:00:00'),
(1, 'marie.durand@residence.com', 'Bienvenue Emma ! Si tu as besoin de quoi que ce soit, nhesites pas.', '2025-06-28 19:15:00'),
(1, 'admin@residence.com', 'Rappel : la fete des voisins aura lieu samedi prochain dans le jardin. Venez nombreux !', '2025-06-29 10:00:00'),
(1, 'lucas.noir@residence.com', 'Super ! Japporterai ma guitare pour lambiance', '2025-06-29 10:30:00'),
(1, 'manon.gris@residence.com', 'Genial ! Je preparerai des gateaux maison', '2025-06-29 11:00:00');

-- Messages Evenements et Festivites
INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(7, 'admin@residence.com', 'Qui a des idees pour la fete des voisins de samedi ?', '2025-06-25 15:00:00'),
(7, 'marie.durand@residence.com', 'On pourrait organiser un barbecue collectif ! Jai un grand barbecue.', '2025-06-25 15:30:00'),
(7, 'julie.petit@residence.com', 'Excellente idee ! Je peux apporter des salades.', '2025-06-25 16:00:00'),
(7, 'sarah.bleu@residence.com', 'Et moi des boissons ! Ca va etre genial !', '2025-06-25 16:15:00'),
(7, 'manon.gris@residence.com', 'Je moccupe des desserts ! Jadore patisser', '2025-06-25 17:00:00');

INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(7, 'elise.bronze@residence.com', 'Super ! Et si on organisait aussi des jeux pour les enfants ?', '2025-06-26 10:00:00'),
(7, 'camille.platine@residence.com', 'Bonne idee Elise ! Jai des jeux de societe quon peut sortir.', '2025-06-26 10:30:00'),
(7, 'alice.dore@residence.com', 'Il faut quon verifie la meteo !', '2025-06-26 11:00:00'),
(7, 'admin@residence.com', 'Meteo annoncee : ensoleille ! Tout est parfait !', '2025-06-26 18:00:00');

-- Messages Travaux et Maintenance
INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(8, 'admin@residence.com', 'Planning des travaux de la semaine : Lundi - peinture hall B, Mercredi - jardinage, Vendredi - nettoyage parkings.', '2025-06-24 08:00:00'),
(8, 'gardien@residence.com', 'Recu. Je coordonnerai avec les equipes. Lacces sera maintenu partout.', '2025-06-24 08:30:00'),
(8, 'paul.moreau@residence.com', 'Y a-t-il possibilite de repeindre aussi les boites aux lettres ? Elles commencent a rouiller.', '2025-06-25 09:00:00'),
(8, 'admin@residence.com', 'Bonne remarque Paul ! Je lajoute a la liste des travaux du mois prochain.', '2025-06-25 10:00:00'),
(8, 'thomas.roux@residence.com', 'La porte dentree du batiment A grince beaucoup, ca pourrait etre huile ?', '2025-06-26 12:00:00');

INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(8, 'gardien@residence.com', 'Je men occupe cet apres-midi Thomas !', '2025-06-26 12:15:00'),
(8, 'kevin.jaune@residence.com', 'Les ampoules du parking B sont grillees, cest assez sombre le soir.', '2025-06-27 19:00:00'),
(8, 'admin@residence.com', 'Note Kevin, remplacement prevu demain matin par lelectricien.', '2025-06-27 19:30:00'),
(8, 'romain.cuivre@residence.com', 'Merci pour votre reactivite ! Cest appreciable davoir une equipe efficace.', '2025-06-27 20:00:00');

-- Messages Covoiturage
INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(9, 'marie.durand@residence.com', 'Salut ! Qui va au centre-ville demain matin vers 9h ? Je peux prendre 2 personnes', '2025-06-28 20:00:00'),
(9, 'thomas.roux@residence.com', 'Moi ! Jai RDV chez le dentiste a 9h30, ca marrangerait !', '2025-06-28 20:15:00'),
(9, 'kevin.jaune@residence.com', 'Super Marie ! Moi aussi je veux bien, jai des courses a faire.', '2025-06-28 20:30:00'),
(9, 'marie.durand@residence.com', 'Parfait ! RDV 8h45 devant lentree principale !', '2025-06-28 20:45:00'),
(9, 'antoine.rouge@residence.com', 'Quelquun va a laeroport vendredi ? Je peux participer aux frais !', '2025-06-29 14:00:00');

INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(9, 'arthur.acier@residence.com', 'Desole Antoine, moi cest samedi. Regarde sur lapp BlaBlaCar ?', '2025-06-29 14:30:00'),
(9, 'gabriel.plomb@residence.com', 'Moi jy vais vendredi Antoine ! A quelle heure ?', '2025-06-29 15:00:00'),
(9, 'antoine.rouge@residence.com', 'Genial Gabriel ! Vol a 16h, donc depart vers 13h30 ?', '2025-06-29 15:15:00'),
(9, 'gabriel.plomb@residence.com', 'Parfait ! MP moi pour les details', '2025-06-29 15:30:00');

-- Messages Voisins Sportifs
INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(10, 'paul.moreau@residence.com', 'Salut les sportifs ! Qui est motive pour un jogging demain matin ?', '2025-06-28 18:00:00'),
(10, 'lucas.noir@residence.com', 'Moi ! Quelle heure et quel parcours ?', '2025-06-28 18:15:00'),
(10, 'hugo.rose@residence.com', 'Count me in ! Jai besoin de me remettre en forme !', '2025-06-28 18:30:00'),
(10, 'paul.moreau@residence.com', '7h devant la residence, on fait le tour du parc (5km environ) ?', '2025-06-28 18:45:00'),
(10, 'nathan.cyan@residence.com', 'Top ! Moi qui cherchais un groupe de running !', '2025-06-28 19:00:00');

INSERT INTO message (id_groupe_message, email_auteur, contenu_message, date_envoi) VALUES
(10, 'theo.argent@residence.com', 'Ah zut, je peux pas demain mais la prochaine fois !', '2025-06-28 19:15:00'),
(10, 'arthur.acier@residence.com', 'Et si on organisait aussi des sessions tennis ? Il y a des courts pres dici', '2025-06-29 16:00:00'),
(10, 'lucas.noir@residence.com', 'Excellente idee Arthur ! Je suis joueur regulier, on peut faire des doubles !', '2025-06-29 16:30:00'),
(10, 'paul.moreau@residence.com', 'Super ! On pourrait faire jogging le matin, tennis lapres-midi ?', '2025-06-29 17:00:00');

-- Insertion des invitations et visites exemples
INSERT INTO invite (email_invite, id_ban, statut_invitation) VALUES
('marie.durand@residence.com', 1, 'acceptee'),
('paul.moreau@residence.com', 2, 'acceptee'),
('sarah.bleu@residence.com', 11, 'acceptee'),
('elise.bronze@residence.com', 19, 'acceptee');

INSERT INTO visite (email_visiteur, id_invitation, motif_visite, statut_visite, date_visite_start,date_visite_end) VALUES
('gardien@residence.com', 1, 'Visite de courtoisie - accueil nouveau resident', 'terminee', '2025-06-25 10:00:00', '2025-06-25 11:00:00'),
('admin@residence.com', 2, 'Inspection technique annuelle', 'terminee', '2025-06-26 14:00:00', '2025-06-26 14:50:00'),
('gardien2@residence.com', 3, 'Controle securite batiment B', 'terminee', '2025-06-27 09:00:00', '2025-06-27 09:05:00'),
('admin2@residence.com', 4, 'Visite de bienvenue', 'programmee', '2025-07-01 10:00:00', '2025-07-01 10:25:00');


