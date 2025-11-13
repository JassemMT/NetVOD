CREATE DATABASE IF NOT EXISTS netvod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE netvod;

-- Table des utilisateurs
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
ALTER TABLE user ADD COLUMN verified BOOLEAN DEFAULT FALSE;

-- Table des séries
CREATE TABLE serie (
    id_serie INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    annee YEAR,
    image VARCHAR(255)
);
ALTER TABLE serie
ADD COLUMN genre VARCHAR(100) AFTER image,
ADD COLUMN public VARCHAR(100) AFTER genre;


-- Table des épisodes
CREATE TABLE episode (
    id_episode INT AUTO_INCREMENT PRIMARY KEY,
    id_serie INT NOT NULL,
    numero INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    duree INT NOT NULL, -- en minutes
    source VARCHAR(255) NOT NULL, -- chemin ou URL de la vidéo
    src_image VARCHAR(255),
    FOREIGN KEY (id_serie) REFERENCES serie(id_serie) ON DELETE CASCADE
);

-- Table des commentaires
CREATE TABLE commentaire (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_serie INT NOT NULL,
    note TINYINT CHECK (note BETWEEN 0 AND 5),
    contenu TEXT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_user, id_serie),
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_serie) REFERENCES serie(id_serie) ON DELETE CASCADE
);

-- Table des favoris
CREATE TABLE User2favori (
    id_user INT NOT NULL,
    id_serie INT NOT NULL,
    PRIMARY KEY (id_user, id_serie),
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_serie) REFERENCES serie(id_serie) ON DELETE CASCADE
);

-- Table des séries en cours
CREATE TABLE User2encours (
    id_user INT NOT NULL,
    id_serie INT NOT NULL,
    PRIMARY KEY (id_user, id_serie),
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_serie) REFERENCES serie(id_serie) ON DELETE CASCADE
);
