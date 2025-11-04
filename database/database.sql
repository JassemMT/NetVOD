
-- Base de données : NetVOD


CREATE DATABASE IF NOT EXISTS netvod
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE netvod;


-- Table : user

CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(255) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
) ENGINE=InnoDB;


-- Table : serie

CREATE TABLE serie (
    id_serie INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    annee YEAR NOT NULL,
    image VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;


-- Table : episode

CREATE TABLE episode (
    id_episode INT AUTO_INCREMENT PRIMARY KEY,
    id_serie INT NOT NULL,
    numero INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    duree INT NOT NULL,
    source VARCHAR(255) NOT NULL,
    src_image VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_episode_serie
        FOREIGN KEY (id_serie)
        REFERENCES serie(id_serie)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT uq_episode_numero UNIQUE (id_serie, numero)
) ENGINE=InnoDB;

-- Table : liste

CREATE TABLE liste (
    id_liste INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    CONSTRAINT fk_liste_user
        FOREIGN KEY (id_user)
        REFERENCES user(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT uq_liste_nom UNIQUE (id_user, nom)
) ENGINE=InnoDB;

-- Table : programme2playlist

CREATE TABLE programme2playlist (
    id_liste INT NOT NULL,
    id_programme INT NOT NULL,
    PRIMARY KEY (id_liste, id_programme),
    CONSTRAINT fk_proglist_liste
        FOREIGN KEY (id_liste)
        REFERENCES liste(id_liste)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_proglist_serie
        FOREIGN KEY (id_programme)
        REFERENCES serie(id_serie)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Table : commentaire

CREATE TABLE commentaire (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_serie INT NOT NULL,
    note TINYINT NOT NULL CHECK (note BETWEEN 1 AND 5),
    contenu TEXT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_commentaire_user
        FOREIGN KEY (id_user)
        REFERENCES user(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_commentaire_serie
        FOREIGN KEY (id_serie)
        REFERENCES serie(id_serie)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT uq_commentaire UNIQUE (id_user, id_serie)
) ENGINE=InnoDB;


