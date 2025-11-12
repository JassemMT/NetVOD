-- Script d'insertion de données pour NetVOD
-- Encodage UTF-8
SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- ========================================
-- INSERTION DES UTILISATEURS
-- ========================================
INSERT INTO `user` (`id_user`, `mail`, `password`) VALUES
(1, 'jack2.surfer@netvod.com', '$2y$10$yga9G2j2PDGV3MukwL54u.dP0PL1HE89Hxsb/bITbSvVqTvxugH.e'), -- password: Jack2024!
(2, 'jim.taxi@netvod.com', '$2y$10$bcdefghijklmnopqrstuvwxyz1234567890'), -- password: Jim2024!
(3, 'marie.plage@netvod.com', '$2y$10$cdefghijklmnopqrstuvwxyz12345678901'), -- password: Marie2024!
(4, 'lucas.mystere@netvod.com', '$2y$10$defghijklmnopqrstuvwxyz123456789012'), -- password: Lucas2024!
(5, 'emma.chevaux@netvod.com', '$2y$10$efghijklmnopqrstuvwxyz1234567890123'); -- password: Emma2024!

-- ========================================
-- INSERTION DES SÉRIES (adaptées du sujet)
-- ========================================
INSERT INTO `serie` (`id_serie`, `titre`, `description`, `annee`, `image`) VALUES
(1, 'Le lac aux mystères', 'C\'est l\'histoire d\'un lac mystérieux et plein de surprises. La série, bluffante et haletante, nous entraine dans un labyrinthe d\'intrigues époustouflantes. A ne rater sous aucun prétexte !', 2020, 'lake_mystere.jpg'),
(2, 'L\'eau a coulé', 'Une série nostalgique qui nous invite à revisiter notre passé et à se remémorer tout ce qui s\'est passé depuis que tant d\'eau a coulé sous les ponts.', 1907, 'eau_coule.jpg'),
(3, 'Chevaux fous', 'Une série sur la vie des chevals sauvages en liberté. Décoiffante.', 2017, 'chevaux_fous.jpg'),
(4, 'A la plage', 'Le succès de l\'été 2021, à regarder sans modération et entre amis.', 2021, 'a_la_plage.jpg'),
(5, 'Champion', 'La vie trépidante de deux champions de surf, passionnés dès leur plus jeune age. Ils consacrent leur vie à ce sport.', 2022, 'champion_surf.jpg'),
(6, 'Une ville la nuit', 'C\'est beau une ville la nuit, avec toutes ces voitures qui passent et qui repassent. La série suit un livreur, un chauffeur de taxi, et un insomniaque. Tous parcourent la grande ville une fois la nuit venue, au volant de leur véhicule.', 2017, 'ville_nuit.jpg');

-- ========================================
-- INSERTION DES ÉPISODES (adaptés du sujet)
-- ========================================
INSERT INTO `episode` (`id_episode`, `id_serie`, `numero`, `titre`, `duree`, `source`, `src_image`) VALUES
-- Série 1: Le lac aux mystères
(1, 1, 1, 'Le lac', 8, 'lake.mp4', 'lake_ep1.jpg'),
(2, 1, 2, 'Le lac : les mystères de l\'eau trouble', 8, 'lake.mp4', 'lake_ep2.jpg'),
(3, 1, 3, 'Le lac : les mystères de l\'eau sale', 8, 'lake.mp4', 'lake_ep3.jpg'),
(4, 1, 4, 'Le lac : les mystères de l\'eau chaude', 8, 'lake.mp4', 'lake_ep4.jpg'),
(5, 1, 5, 'Le lac : les mystères de l\'eau froide', 8, 'lake.mp4', 'lake_ep5.jpg'),

-- Série 2: L'eau a coulé
(6, 2, 1, 'Eau calme', 15, 'water.mp4', 'water_ep1.jpg'),
(7, 2, 2, 'Eau calme 2', 15, 'water.mp4', 'water_ep2.jpg'),
(8, 2, 3, 'Eau moins calme', 15, 'water.mp4', 'water_ep3.jpg'),
(9, 2, 4, 'la tempête', 15, 'water.mp4', 'water_ep4.jpg'),
(10, 2, 5, 'Le calme après la tempête', 15, 'water.mp4', 'water_ep5.jpg'),

-- Série 3: Chevaux fous
(11, 3, 1, 'les chevaux s\'amusent', 7, 'horses.mp4', 'horses_ep1.jpg'),
(12, 3, 2, 'les chevals fous', 7, 'horses.mp4', 'horses_ep2.jpg'),
(13, 3, 3, 'les chevaux de l\'étoile noire', 7, 'horses.mp4', 'horses_ep3.jpg'),

-- Série 4: A la plage
(14, 4, 1, 'Tous à la plage', 18, 'beach.mp4', 'beach_ep1.jpg'),
(15, 4, 2, 'La plage le soir', 18, 'beach.mp4', 'beach_ep2.jpg'),
(16, 4, 3, 'La plage le matin', 18, 'beach.mp4', 'beach_ep3.jpg'),

-- Série 5: Champion
(17, 5, 1, 'champion de surf', 11, 'surf.mp4', 'surf_ep1.jpg'),
(18, 5, 2, 'surf détective', 11, 'surf.mp4', 'surf_ep2.jpg'),
(19, 5, 3, 'surf amitié', 11, 'surf.mp4', 'surf_ep3.jpg'),

-- Série 6: Une ville la nuit
(20, 6, 1, 'Ça roule, ça roule', 27, 'cars-by-night.mp4', 'cars_ep1.jpg'),
(21, 6, 2, 'Ça roule, ça roule toujours', 27, 'cars-by-night.mp4', 'cars_ep2.jpg');

-- ========================================
-- INSERTION DES COMMENTAIRES
-- ========================================
INSERT INTO `commentaire` (`id_commentaire`, `id_user`, `id_serie`, `note`, `contenu`, `date`) VALUES
(1, 1, 5, 5, 'Excellent ! En tant que surfeur, je me reconnais totalement dans cette série. Les scènes de surf sont très réalistes.', '2024-10-15 14:30:00'),
(2, 1, 6, 4, 'Super ambiance nocturne, ça me rappelle mes trajets pour aller surfer tôt le matin.', '2024-10-20 09:15:00'),
(3, 2, 6, 5, 'En tant que chauffeur de taxi de nuit, je trouve cette série très fidèle à la réalité. Bravo !', '2024-10-18 22:45:00'),
(4, 2, 1, 4, 'Mystérieux à souhait ! J\'attends la suite avec impatience.', '2024-10-12 18:20:00'),
(5, 3, 4, 5, 'Le soleil, la mer, le sable... Cette série est parfaite pour rêver de vacances !', '2024-11-01 16:00:00'),
(6, 3, 2, 3, 'Intéressante mais un peu lente par moments. Bonne pour la nostalgie.', '2024-10-25 20:30:00'),
(7, 4, 1, 5, 'Quelle série captivante ! Chaque épisode apporte son lot de mystères. Un chef-d\'œuvre !', '2024-10-14 19:45:00'),
(8, 4, 3, 4, 'Originale et drôle ! Les chevaux sont attachants.', '2024-10-28 15:10:00'),
(9, 5, 3, 5, 'Magnifique ! Je suis passionnée de chevaux et cette série est juste parfaite.', '2024-10-30 11:20:00'),
(10, 5, 5, 4, 'Belle histoire d\'amitié et de passion pour le surf.', '2024-11-02 13:55:00');

-- ========================================
-- INSERTION DES FAVORIS
-- ========================================
INSERT INTO `User2favori` (`id_user`, `id_serie`) VALUES
-- Jack adore le surf et les voitures
(1, 5), -- Champion
(1, 6), -- Une ville la nuit
(1, 1), -- Le lac aux mystères

-- Jim aime les mystères et la vie nocturne
(2, 1), -- Le lac aux mystères
(2, 6), -- Une ville la nuit
(2, 2), -- L'eau a coulé

-- Marie aime la plage et la détente
(3, 4), -- A la plage
(3, 2), -- L'eau a coulé
(3, 5), -- Champion

-- Lucas aime les mystères et l'originalité
(4, 1), -- Le lac aux mystères
(4, 3), -- Chevaux fous
(4, 6), -- Une ville la nuit

-- Emma adore les chevaux et les histoires d'amitié
(5, 3), -- Chevaux fous
(5, 5), -- Champion
(5, 4); -- A la plage

-- ========================================
-- INSERTION DES SÉRIES EN COURS
-- ========================================
INSERT INTO `User2encours` (`id_user`, `id_serie`) VALUES
-- Jack regarde actuellement
(1, 1), -- Le lac aux mystères (en train de découvrir)
(1, 5), -- Champion (rewatch)

-- Jim regarde actuellement
(2, 6), -- Une ville la nuit (série qui le concerne)
(2, 2), -- L'eau a coulé

-- Marie regarde actuellement
(3, 4), -- A la plage
(3, 5), -- Champion

-- Lucas regarde actuellement
(4, 1), -- Le lac aux mystères
(4, 3), -- Chevaux fous

-- Emma regarde actuellement
(5, 3), -- Chevaux fous (sa préférée)
(5, 1); -- Le lac aux mystères

-- ========================================
-- FIN DU SCRIPT
-- ========================================
SET foreign_key_checks = 1;

-- Vérification des données insérées
SELECT 'Utilisateurs insérés:' AS Info, COUNT(*) AS Nombre FROM user
UNION ALL
SELECT 'Séries insérées:', COUNT(*) FROM serie
UNION ALL
SELECT 'Épisodes insérés:', COUNT(*) FROM episode
UNION ALL
SELECT 'Commentaires insérés:', COUNT(*) FROM commentaire
UNION ALL
SELECT 'Favoris insérés:', COUNT(*) FROM User2favori
UNION ALL
SELECT 'Séries en cours:', COUNT(*) FROM User2encours;