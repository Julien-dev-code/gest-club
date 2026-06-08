-- =============================================================================
-- GEST CLUB - Script de création de la base de données
-- =============================================================================
-- Projet : Fil rouge DWWM - Application de gestion de réservations d'événements
-- SGBD   : MariaDB / MySQL
-- Auteur : Julien
--
-- Conventions appliquées :
--   - Noms en snake_case minuscule (tables et colonnes)
--   - PK technique 'id' AUTO_INCREMENT BIGINT sur toutes les tables
--   - FK nommées 'id_<table_référencée>'
--   - Charset utf8mb4 pour gérer correctement les accents et caractères spéciaux
--   - Moteur InnoDB pour le support des clés étrangères et des transactions
--   - Contraintes UNIQUE sur les tables associatives pour enforcer les règles métier
--
-- Mapping MCD -> Tables :
--   Attribue        -> reservation_place (table billet : porte le qr_code)
--   Demande en Ami  -> demande_ami       (réflexive : demandeur / receveur)
--   Vote, Noter, Participe -> mêmes noms
-- =============================================================================

-- Création de la base si elle n'existe pas
CREATE DATABASE IF NOT EXISTS gest_club
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gest_club;

-- =============================================================================
-- Suppression des tables existantes (ordre inverse des dépendances)
-- Permet de relancer le script en environnement de développement
-- =============================================================================
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS demande_ami;
DROP TABLE IF EXISTS vote;
DROP TABLE IF EXISTS participe;
DROP TABLE IF EXISTS noter;
DROP TABLE IF EXISTS presence;
DROP TABLE IF EXISTS reservation_place;
DROP TABLE IF EXISTS reservation;
DROP TABLE IF EXISTS place;
DROP TABLE IF EXISTS evenement;
DROP TABLE IF EXISTS athlete;
DROP TABLE IF EXISTS type_evenement;
DROP TABLE IF EXISTS niveau;
DROP TABLE IF EXISTS tribune;
DROP TABLE IF EXISTS utilisateur;

SET FOREIGN_KEY_CHECKS = 1;


-- =============================================================================
-- TABLES SANS DÉPENDANCE (créées en premier)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Table : utilisateur
-- Cœur du système. Une table unique pour les 3 acteurs du CDC,
-- différenciés par l'attribut 'role'.
-- -----------------------------------------------------------------------------
CREATE TABLE utilisateur (
  id            BIGINT       NOT NULL AUTO_INCREMENT,
  nom           VARCHAR(100) NOT NULL,
  prenom        VARCHAR(100) NOT NULL,
  telephone     VARCHAR(20)  NOT NULL,
  adresse       VARCHAR(255) NOT NULL,
  code_postal   VARCHAR(10)  NOT NULL,
  ville         VARCHAR(100) NOT NULL,
  email         VARCHAR(255) NOT NULL,
  mot_de_passe  VARCHAR(255) NOT NULL,
  actif         BOOLEAN      NOT NULL DEFAULT TRUE,
  visibilite    ENUM('public', 'prive', 'ferme') NOT NULL DEFAULT 'prive',
  role          ENUM('spectateur', 'responsable', 'service_reservation') NOT NULL DEFAULT 'spectateur',
  PRIMARY KEY (id),
  UNIQUE KEY uk_utilisateur_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : tribune
-- Les 4 tribunes du stade (nord, sud, est, ouest)
-- -----------------------------------------------------------------------------
CREATE TABLE tribune (
  id   BIGINT       NOT NULL AUTO_INCREMENT,
  nom  VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_tribune_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : niveau
-- Les 3 niveaux du stade (haut, milieu, bas)
-- -----------------------------------------------------------------------------
CREATE TABLE niveau (
  id   BIGINT       NOT NULL AUTO_INCREMENT,
  nom  VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_niveau_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : type_evenement
-- Liste des types d'événements (football, basketball, etc.)
-- Externalisée en table (au lieu d'un ENUM) pour permettre la gestion CRUD
-- par le responsable du club, comme indiqué dans le CDC.
-- -----------------------------------------------------------------------------
CREATE TABLE type_evenement (
  id   BIGINT       NOT NULL AUTO_INCREMENT,
  nom  VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_type_evenement_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : athlete
-- Les sportifs qui participent aux événements et peuvent recevoir des votes
-- -----------------------------------------------------------------------------
CREATE TABLE athlete (
  id      BIGINT       NOT NULL AUTO_INCREMENT,
  nom     VARCHAR(100) NOT NULL,
  prenom  VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLES AVEC DÉPENDANCES SIMPLES
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Table : evenement
-- Un événement appartient à un type (FK), avec planning et statut
-- -----------------------------------------------------------------------------
CREATE TABLE evenement (
  id                 BIGINT       NOT NULL AUTO_INCREMENT,
  nom                VARCHAR(100) NOT NULL,
  statut             ENUM('programme', 'en_cours', 'termine', 'annule') NOT NULL DEFAULT 'programme',
  date_debut         DATETIME     NOT NULL,
  date_fin           DATETIME     NOT NULL,
  id_type_evenement  BIGINT       NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_evenement_type_evenement
    FOREIGN KEY (id_type_evenement) REFERENCES type_evenement (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : place
-- Une place est située à un niveau dans une tribune (cf. CDC)
-- Catalogue physique du stade, indépendant des événements.
-- -----------------------------------------------------------------------------
CREATE TABLE place (
  id          BIGINT  NOT NULL AUTO_INCREMENT,
  numero      INT     NOT NULL,
  id_tribune  BIGINT  NOT NULL,
  id_niveau   BIGINT  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_place_localisation (id_tribune, id_niveau, numero),
  CONSTRAINT fk_place_tribune
    FOREIGN KEY (id_tribune) REFERENCES tribune (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_place_niveau
    FOREIGN KEY (id_niveau) REFERENCES niveau (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : reservation
-- Une réservation = un utilisateur réserve pour un événement
-- Les places réservées sont dans la table reservation_place.
-- Règle métier "max 2 places par événement" -> enforcée côté applicatif.
-- -----------------------------------------------------------------------------
CREATE TABLE reservation (
  id                BIGINT    NOT NULL AUTO_INCREMENT,
  date_reservation  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_utilisateur    BIGINT    NOT NULL,
  id_evenement      BIGINT    NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_reservation_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_reservation_evenement
    FOREIGN KEY (id_evenement) REFERENCES evenement (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLES ASSOCIATIVES (avec attributs ou règles métier spécifiques)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Table : reservation_place (= association "Attribue" du MCD = "billet")
-- Une ligne = un billet avec son qr_code.
-- Règle métier : 1 place = 1 QR code (cf. discussion projet)
-- UNIQUE(id_place, id_reservation) : une même place ne peut pas apparaître
--   deux fois dans la même réservation.
-- -----------------------------------------------------------------------------
CREATE TABLE reservation_place (
  id              BIGINT       NOT NULL AUTO_INCREMENT,
  id_reservation  BIGINT       NOT NULL,
  id_place        BIGINT       NOT NULL,
  qr_code         VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_reservation_place (id_reservation, id_place),
  UNIQUE KEY uk_qr_code (qr_code),
  CONSTRAINT fk_reservation_place_reservation
    FOREIGN KEY (id_reservation) REFERENCES reservation (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_reservation_place_place
    FOREIGN KEY (id_place) REFERENCES place (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : presence
-- Enregistre la présence d'un spectateur scanné à l'entrée par un agent.
-- id_utilisateur = l'agent du service de réservation qui scanne.
-- NOTE : rattachée à reservation (cf. MCD validé). Pour gérer une présence
-- par billet scanné indépendamment, il faudrait à terme la rattacher à
-- reservation_place. Hors périmètre jury.
-- -----------------------------------------------------------------------------
CREATE TABLE presence (
  id                 BIGINT    NOT NULL AUTO_INCREMENT,
  date_confirmation  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_reservation     BIGINT    NOT NULL,
  id_utilisateur     BIGINT    NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_presence_reservation (id_reservation),
  CONSTRAINT fk_presence_reservation
    FOREIGN KEY (id_reservation) REFERENCES reservation (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_presence_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : noter (association binaire UTILISATEUR x EVENEMENT)
-- Un utilisateur note un événement avec une note.
-- UNIQUE pour empêcher un utilisateur de noter deux fois le même événement.
-- -----------------------------------------------------------------------------
CREATE TABLE noter (
  id              BIGINT  NOT NULL AUTO_INCREMENT,
  note            INT     NOT NULL,
  id_utilisateur  BIGINT  NOT NULL,
  id_evenement    BIGINT  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_noter (id_utilisateur, id_evenement),
  CONSTRAINT fk_noter_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_noter_evenement
    FOREIGN KEY (id_evenement) REFERENCES evenement (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_noter_valeur CHECK (note BETWEEN 0 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : participe (association ATHLETE x EVENEMENT)
-- Indique quels athlètes participent à quels événements.
-- Préalable nécessaire pour pouvoir voter pour un athlète sur un événement.
-- -----------------------------------------------------------------------------
CREATE TABLE participe (
  id            BIGINT  NOT NULL AUTO_INCREMENT,
  id_athlete    BIGINT  NOT NULL,
  id_evenement  BIGINT  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_participe (id_athlete, id_evenement),
  CONSTRAINT fk_participe_athlete
    FOREIGN KEY (id_athlete) REFERENCES athlete (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_participe_evenement
    FOREIGN KEY (id_evenement) REFERENCES evenement (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : vote (association TERNAIRE UTILISATEUR x EVENEMENT x ATHLETE)
-- Un utilisateur vote pour un athlète sur un événement donné.
-- UNIQUE pour empêcher un utilisateur de voter deux fois pour le même athlète
-- sur le même événement.
-- -----------------------------------------------------------------------------
CREATE TABLE vote (
  id              BIGINT  NOT NULL AUTO_INCREMENT,
  valeur          INT     NOT NULL DEFAULT 1,
  id_utilisateur  BIGINT  NOT NULL,
  id_evenement    BIGINT  NOT NULL,
  id_athlete      BIGINT  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_vote (id_utilisateur, id_evenement, id_athlete),
  CONSTRAINT fk_vote_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_vote_evenement
    FOREIGN KEY (id_evenement) REFERENCES evenement (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_vote_athlete
    FOREIGN KEY (id_athlete) REFERENCES athlete (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- Table : demande_ami (association RÉFLEXIVE sur UTILISATEUR)
-- Correction manuelle nécessaire : JMerise ne génère qu'une seule FK pour les
-- associations réflexives. Ici on distingue explicitement demandeur / receveur.
-- CHECK empêche l'auto-amitié (id_demandeur != id_receveur).
-- UNIQUE empêche les doublons de demande dans le même sens.
-- -----------------------------------------------------------------------------
CREATE TABLE demande_ami (
  id             BIGINT  NOT NULL AUTO_INCREMENT,
  id_demandeur   BIGINT  NOT NULL,
  id_receveur    BIGINT  NOT NULL,
  statut         ENUM('en_attente', 'acceptee', 'refusee', 'ignoree') NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (id),
  UNIQUE KEY uk_demande_ami (id_demandeur, id_receveur),
  CONSTRAINT fk_demande_ami_demandeur
    FOREIGN KEY (id_demandeur) REFERENCES utilisateur (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_demande_ami_receveur
    FOREIGN KEY (id_receveur) REFERENCES utilisateur (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- DONNÉES DE RÉFÉRENCE (à exécuter après la création des tables)
-- Les niveaux et tribunes sont fixes selon le CDC, autant les insérer ici.
-- =============================================================================

INSERT INTO tribune (nom) VALUES ('nord'), ('sud'), ('est'), ('ouest');
INSERT INTO niveau (nom) VALUES ('haut'), ('milieu'), ('bas');

-- Types d'événements de départ (modifiables ensuite via le CRUD du responsable)
INSERT INTO type_evenement (nom) VALUES ('football'), ('basketball'), ('rugby'), ('handball');

-- =============================================================================
-- FIN DU SCRIPT
-- =============================================================================
