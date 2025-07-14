CREATE DATABASE IF NOT EXISTS emprunt_objet;
USE emprunt_objet;

-- 1. Table des membres (aucune dépendance)
CREATE TABLE membre (
    id_membre INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    date_naissance DATE,
    genre ENUM('Homme', 'Femme', 'Autre'),
    email VARCHAR(150) UNIQUE,
    ville VARCHAR(100),
    mdp VARCHAR(255),
    image_profil VARCHAR(255)
);

-- 2. Table des catégories (aucune dépendance)
CREATE TABLE categorie_objet (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(100)
);

-- 3. Table des objets (dépend de membre et categorie_objet)
CREATE TABLE objet (
    id_objet INT AUTO_INCREMENT PRIMARY KEY,
    nom_objet VARCHAR(100),
    id_categorie INT,
    id_membre INT,
    FOREIGN KEY (id_categorie) REFERENCES categorie_objet(id_categorie),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

-- 4. Table des images (dépend de objet)
CREATE TABLE images_objet (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    nom_image VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet)
);

-- 5. Table des emprunts (dépend de objet et membre)
CREATE TABLE emprunt (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT,
    id_membre INT,
    date_emprunt DATE,
    date_retour DATE,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

-- Données membres
INSERT INTO membre (id_membre, nom, email, mdp) VALUES
(1, 'Fabrice', 'fabrice@email.com', 'fabrice'),
(2, 'Alice', 'alice@email.com', 'alice'),
(3, 'Bob', 'bob@email.com', 'bob');

-- Données catégories
INSERT INTO categorie_objet (nom_categorie) VALUES
('Esthétique'), ('Bricolage'), ('Mécanique'), ('Cuisine');

-- Données objets
INSERT INTO objet (id_objet, nom_objet, id_categorie, id_membre) VALUES
(1, 'Perceuse Bosch', 2, 1),
(2, 'Fer à lisser', 1, 1),
(3, 'Tournevis électrique', 2, 2),
(4, 'Miroir lumineux', 1, 2),
(5, 'Casserole Inox', 4, 1),
(6, 'Clé à molette', 3, 2),
(7, 'Friteuse', 4, 1),
(8, 'Sèche-cheveux', 1, 3),
(9, 'Perceuse murale', 2, 3),
(10, 'Robot de cuisine', 4, 2);

-- Données images objets
INSERT INTO images_objet (id_objet, nom_image) VALUES
(1, 'perceuse_bosch.jpg'),
(2, 'fer_a_lisser.jpg'),
(3, 'tournevis_electrique.jpg'),
(4, 'miroir_lumineux.jpg'),
(5, 'casserole_inox.jpg'),
(6, 'cle_a_molette.jpg'),
(7, 'friteuse.jpg'),
(8, 'seche_cheveux.jpg'),
(9, 'perceuse_murale.jpg'),
(10, 'robot_cuisine.jpg');

-- Données emprunts
INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
(1, 2, '2025-07-01', '2025-07-10'),
(2, 3, '2025-07-05', '2025-07-15'),
(3, 1, '2025-07-07', '2025-07-14'),
(4, 1, '2025-07-08', '2025-07-18'),
(5, 3, '2025-07-05', '2025-07-11'),
(6, 1, '2025-07-03', '2025-07-12'),
(7, 2, '2025-07-04', '2025-07-14'),
(8, 1, '2025-07-06', '2025-07-16'),
(9, 2, '2025-07-02', '2025-07-13'),
(10, 3, '2025-07-01', '2025-07-11');
