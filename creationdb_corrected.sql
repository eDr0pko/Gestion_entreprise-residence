-- Gestion d'entreprise de résidence

-- Creation de la base de données
CREATE DATABASE IF NOT EXISTS gestion_entreprise_residence;
USE gestion_entreprise_residence;
DROP TABLE IF EXISTS visite, invite, message, personne_groupe, groupe_message, ban, resident, gardien, admin, personne;


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
  date_visite TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut_visite ENUM('programmee', 'en_cours', 'terminee', 'annulee') DEFAULT 'programmee',
  PRIMARY KEY(id_visite),
  INDEX visite_FKIndex1(email_visiteur),
  INDEX visite_FKIndex2(id_invitation),
  FOREIGN KEY(email_visiteur) REFERENCES personne(email) ON DELETE CASCADE,
  FOREIGN KEY(id_invitation) REFERENCES invite(id_invitation) ON DELETE CASCADE
);


-- INSERTION D'un jeu de test

-- Insertion des personnes
INSERT INTO personne (email, nom, prenom, mot_de_passe, numero_telephone) VALUES
('admin@residence.com', 'Dupont', 'Jean', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789'),
('gardien@residence.com', 'Martin', 'Pierre', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0234567890'),
('resident@residence.com', 'Durand', 'Marie', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0345678901'),
('resident2@residence.com', 'Moreau', 'Paul', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0456789012');

-- Insertion de l'admin
INSERT INTO admin (email_personne, mot_de_passe_admin) VALUES
('admin@residence.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertion du gardien
INSERT INTO gardien (email_personne) VALUES
('gardien@residence.com');

-- Insertion des résidents
INSERT INTO resident (email_personne, adresse_logement) VALUES
('resident@residence.com', 'Bâtiment A - Appartement 101'),
('resident2@residence.com', 'Bâtiment B - Appartement 205');

-- Insertion des bans (logements)
INSERT INTO ban (email_proprietaire) VALUES
('resident@residence.com'),
('resident2@residence.com');

-- Insertion des groupes de message
INSERT INTO groupe_message (nom_groupe) VALUES
('Groupe Général Résidence'),
('Bâtiment A - Communication'),
('Gardiennage et Sécurité'),
('Administration');

-- Insertion des liaisons personne-groupe avec connexions
INSERT INTO personne_groupe (email_personne, id_groupe_message, derniere_connexion) VALUES
-- Admin dans tous les groupes
('admin@residence.com', 1, '2024-06-30 14:30:00'),
('admin@residence.com', 2, '2024-06-30 14:25:00'),
('admin@residence.com', 3, '2024-06-30 14:20:00'),
('admin@residence.com', 4, '2024-06-30 14:35:00'),

-- Gardien dans groupes pertinents
('gardien@residence.com', 1, '2024-06-30 13:45:00'),
('gardien@residence.com', 3, '2024-06-30 13:50:00'),

-- Résidents dans leurs groupes
('resident@residence.com', 1, '2024-06-30 12:15:00'),
('resident@residence.com', 2, '2024-06-30 12:20:00'),
('resident2@residence.com', 1, '2024-06-30 11:30:00'),

-- Ajout de quelques membres supplémentaires pour enrichir les données
('resident2@residence.com', 2, '2024-06-30 10:45:00'),
('gardien@residence.com', 4, '2024-06-30 09:30:00');

-- Insertion des messages d'exemple
INSERT INTO message (id_groupe_message, email_auteur, contenu_message) VALUES
(1, 'admin@residence.com', 'Bienvenue dans le système de gestion de la résidence !'),
(1, 'gardien@residence.com', 'Je suis disponible pour toute urgence 24h/24.'),
(2, 'resident@residence.com', 'Bonjour aux voisins du bâtiment A !'),
(3, 'gardien@residence.com', 'Rapport de sécurité : Tout est normal ce soir.'),
(4, 'admin@residence.com', 'Réunion administrative prévue vendredi prochain.');

-- Insertion des invitations d'exemple
INSERT INTO invite (email_invite, id_ban, statut_invitation) VALUES
('resident@residence.com', 1, 'acceptee'),
('resident2@residence.com', 2, 'acceptee');

-- Insertion des visites d'exemple
INSERT INTO visite (email_visiteur, id_invitation, motif_visite, statut_visite) VALUES
('gardien@residence.com', 1, 'Visite de courtoisie', 'terminee'),
('admin@residence.com', 2, 'Inspection technique', 'programmee');


