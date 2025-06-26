CREATE TABLE admin (
  email VARCHAR(255) NOT NULL AUTO_INCREMENT,
  mpd VARCHAR(255) NOT NULL,
  PRIMARY KEY(email)
);

CREATE TABLE ban (
  idban INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  personne_email VARCHAR(255) NOT NULL,
  PRIMARY KEY(idban, personne_email),
  INDEX ban_FKIndex1(personne_email)
);

CREATE TABLE gardien (
  idgardien INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  personne_email VARCHAR(255) NOT NULL,
  PRIMARY KEY(idgardien, personne_email),
  INDEX gardien_FKIndex1(personne_email)
);

CREATE TABLE groupe_message (
  idgroupe_message INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(idgroupe_message)
);

CREATE TABLE Invite (
  idinvite INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  personne_email VARCHAR(255) NOT NULL,
  ban_personne_email VARCHAR(255) NOT NULL,
  ban_idban INTEGER UNSIGNED NOT NULL,
  photo VARCHAR(255) NULL,
  PRIMARY KEY(idinvite, personne_email, ban_personne_email, ban_idban),
  INDEX Invite_FKIndex1(personne_email),
  INDEX Invite_FKIndex2(ban_idban, ban_personne_email)
);

CREATE TABLE message (
  idmessage INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  groupe_message_idgroupe_message INTEGER UNSIGNED NOT NULL,
  texte TEXT NOT NULL,
  date TIMESTAMP NOT NULL,
  PRIMARY KEY(idmessage, groupe_message_idgroupe_message),
  INDEX message_FKIndex1(groupe_message_idgroupe_message)
);

CREATE TABLE personne (
  email VARCHAR(255) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(45) NOT NULL,
  prenom VARCHAR(45) NOT NULL,
  mdp VARCHAR(255) NOT NULL,
  telephone VARCHAR(20) NOT NULL,
  PRIMARY KEY(email)
);

CREATE TABLE resident (
  idresident INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  personne_email VARCHAR(255) NOT NULL,
  localisation VARCHAR(255) NOT NULL,
  PRIMARY KEY(idresident, personne_email),
  INDEX resident_FKIndex1(personne_email)
);

CREATE TABLE visite (
  idvisite INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  personne_email VARCHAR(255) NOT NULL,
  Invite_personne_email VARCHAR(255) NOT NULL,
  Invite_idinvite INTEGER UNSIGNED NOT NULL,
  Invite_ban_idban INTEGER UNSIGNED NOT NULL,
  Invite_ban_personne_email VARCHAR(255) NOT NULL,
  motif TEXT NULL,
  PRIMARY KEY(idvisite, personne_email, Invite_personne_email, Invite_idinvite, Invite_ban_idban, Invite_ban_personne_email),
  INDEX visite_FKIndex1(personne_email),
  INDEX visite_FKIndex2(Invite_idinvite, Invite_personne_email, Invite_ban_personne_email, Invite_ban_idban)
);


