# NetVOD — SAE « Développer une application web sécurisée »
IUT Nancy-Charlemagne — BUT Informatique S3  
SAE : Développement Web — Sujet : NetVOD

# Membres du groupe

-CHEBBAH Yanis
-ARDHUIN Louis
-GOFFIN Thomas Charles
-TAMOURGH Jassem
-GILBERT Ambroise

## Contexte
NetVOD est une application de vidéo à la demande. Cette SAE vise à développer une version réduite de la plateforme en appliquant les bonnes pratiques de développement web et de sécurité vues en cours (programmation web, bases de données, cryptographie, contrôles d'accès, protection contre les injections, etc.).

Le rendu attendu comprend :
- Un dépôt Git public contenant tout le code (dernier commit autorisé : vendredi 14 novembre à 20h).
- Une application installée et opérationnelle sur le serveur webetu (disponible au plus tard lundi 17 novembre à 20h).
- Un document déposé sur l’espace Arche (avant lundi 17 novembre à 20h) listant membres, URL git/webetu, fonctionnalités réalisées, répartition du travail et comptes de test.

## Objectifs pédagogiques
- Mettre en œuvre une application web complète (frontend, backend, base de données).
- Appliquer les bonnes pratiques d’architecture (OO, namespaces, autoload, Dispatcher/Action).
- Assurer la sécurité : gestion sécurisée des mots de passe, authentification, contrôles d’accès, prévention des injections SQL/XSS.
- Utiliser la cryptographie et produire un travail en anglais technique si nécessaire.

## Périmètre du projet (version à réaliser)
Version restreinte — uniquement les séries (pas de saisons distinctes, pas de films ni documentaires).  
Fonctionnalités à implémenter (prioritaires — fonctionnalités de base) :

Fonctionnalités de base
1. Identification / Authentification (login par email + mot de passe).
2. Inscription (double saisie du mot de passe, contrôle de qualité).
3. Affichage du catalogue des séries (liste avec titre et image).
4. Page détaillée d’une série + liste de ses épisodes (numéro, titre, durée, image).
5. Affichage/visionnage d’un épisode (détail : image, titre, résumé, durée).
6. Ajout d’une série aux préférences d’un utilisateur (bouton « ajouter à mes préférences »).
7. Page d’accueil utilisateur affichant ses séries préférées.
8. Lors du visionnage d’un épisode, ajout automatique de la série à la liste « en cours ».
9. Lors du visionnage, possibilité de noter (1–5) et commenter la série (1 note + 1 commentaire par utilisateur par série).
10. Affichage de la note moyenne d’une série et accès aux commentaires.

Fonctionnalités étendues (si le temps le permet, donnent un bonus)
11. Activation de compte via token éphémère (URL affichée après inscription pour simplifier).
12. Recherche par mots-clés (titre / descriptif).
13. Tri du catalogue (titre, date d’ajout, nombre d’épisodes).
14. Filtrage du catalogue par genre / public ciblé.
15. Retrait d’une série de la liste de préférences.
16. Gestion de la liste « déjà visionnées » (quand tous les épisodes d’une série ont été vus).
17. Gestion du profil utilisateur (nom, prénom, genre préféré...).
18. Accès direct à l’épisode à visionner depuis une série « en cours ».
19. Tri du catalogue par note moyenne.
20. Mot de passe oublié ← génération d’un token éphémère (URL pour réinitialisation).

## Architecture & conventions recommandées
- Classes en PHP (ou langage choisi) avec namespaces et autoload (PSR-4 si PHP).
- Dispatcher / Action pour le routage et la gestion des requêtes.
- Accès aux données via Data Access Object / Repository.
- Stockage sécurisé des mots de passe : password_hash (bcrypt/argon2) ou équivalent.
- Protection contre les injections SQL : requêtes préparées.
- Protection contre XSS : échapper toute sortie (HTML encode).

## Tech stack 
- Backend : PHP 
- Base de données : MySQL 
- Frontend : HTML5, CSS3
- Authentification : sessions + tokens (pour activation / reset)
- Déploiement : serveur webetu (instructions de déploiement à fournir dans la documentation)

> Remarque : adaptez la stack selon les compétences de l’équipe, mais documentez clairement le choix et les étapes d'installation.

## Sécurité — points obligatoires à traiter
- Hashage et vérification sécurisés des mots de passe.
- Contrôle d’accès : pages et APIs accessibles uniquement aux utilisateurs authentifiés quand nécessaire.
- Requêtes SQL sécurisées (utilisation de requêtes préparées).
- Échappement systématique des sorties pour éviter les XSS.
- Mise en place d’un contrôle de la qualité des mots de passe à l’inscription.
- Protection CSRF (tokens sur formulaires critiques).
- Journalisation des accès et erreurs (sans divulguer d’informations sensibles).


