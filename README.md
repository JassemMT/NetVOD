# NetVOD — SAE « Développer une application web sécurisée »
IUT Nancy-Charlemagne — BUT Informatique S3  
SAE : Développement Web — Sujet : NetVOD

# Membres du groupe

-CHEBBAH Yanis
-ARDHUIN Louis
-GOFFIN Thomas Charles
-TAMOURGH Jassem
-GILBERT Ambroise

## URL Git & Webetu

- lien Git : https://github.com/JassemMT/NetVOD/
- lien Webetu : https://webetu.iutnc.univ-lorraine.fr/www/e52526u/NetVOD



## Contexte
NetVOD est une application de vidéo à la demande. Cette SAE vise à développer une version réduite de la plateforme en appliquant les bonnes pratiques de développement web et de sécurité vues en cours (programmation web, bases de données, cryptographie, contrôles d'accès, protection contre les injections, etc.).


## Périmètre du projet
Fonctionnalités Réalisés (prioritaires — fonctionnalités de base) :


Fonctionnalités de base

| Numéro Fonctionnalité   | Fonctionnalité    | Description |  Statut|
| ------------- | ------------- |  -----|-----|
| 1| Identification / Authentification | Permettre à l'utilisateur de se connecter à son compte via son email et son mot de passe |FAIT|
| 2 | Inscription | Permettre à l'utilisateur de créer un nouveau compte en insérant son email et son mot de passe deux fois | FAIT|
| 3 | Affichage du catalogue des séries | Permettre à l'utilisateur d'afficher le contenu du catalogue |FAIT|
| 4 | Page détaillée d’une série + liste de ses épisodes | Permettre à l'utilisateur d'afficher le contenu de chaque série ainsi que les épisodes qui la composent |FAIT|
| 5 | Affichage/visionnage d’un épisode | L'utilisateur a la possibilité de visionner un épisode choisis |FAIT|
| 6 | Ajout d’une série aux préférences d’un utilisateur | Permettre à l'utilisateur d'ajouter une série dans sa liste de préférence |FAIT|
| 7 | Page d’accueil utilisateur affichant ses séries préférées | Permettre à l'utilisateur de voir sa liste de préférence sur la page d'acceuil du site | FAIT |
| 8 | Lors du visionnage d’un épisode, ajout automatique de la série à la liste « en cours » | L'épisode de la série en cours est directement inséré dans la liste 'en_cours' |FAIT|
| 9 | Lors du visionnage, possibilité de noter et commenter la série| Permettre à l'utilisateur de noter la série (1-5) ainsi que de commenter la série (1 note + 1 commentaire par utilisateur par série) |FAIT|
| 10 | Ajout d’une série aux préférences d’un utilisateur | Permettre à l'utilisateur d'ajouter une série dans sa liste de préférence |NON|


Fonctionnalités étendues

| Numéro Fonctionnalité   | Fonctionnalité    | Description |  Statut|
| ------------- | ------------- |  -----|-----|
| 11 | Activation de compte via token éphémère | Permettre à l'utilisateur d'activer son compte via un token éphémère | FAIT |
| 12 | Recherche par mots-clés | Permettre à l'utilisateur de rechercher une série spécifique via des mots-clés | FAIT |
| 13 | Tri du catalogue (titre, date d’ajout, nombre d’épisodes) | Permettre à l'utilisateur d'afficher le contenu du catalogue en triant celui-ci via le titre, la date d'ajout, nombre d'épisodes) | FAIT |
| 14 | Filtrage du catalogue par genre / public ciblé | Permettre à l'utilisateur d'afficher le contenu du catalogue filtrer par un genre ou par public cible | Fait |
| 15 | Retrait d’une série de la liste de préférences | L'utilisateur a la possibilité de supprimer une série de sa liste de préférence | non |
| 16 | Gestion de la liste « déjà visionnées » | Permettre à l'utilisateur de gérer sa liste de série déjà visionnées | NON |
| 17 | Gestion du profil utilisateur | Permettre à l'utilisateur de gérer son profil et ses informations | FAIT |
| 18 | Accès direct à l’épisode à visionner depuis une série « en cours » | L'épisode de la série en cours est directement inséré dans la liste 'en_cours' |NON|
| 19 | Tri du catalogue par note moyenne | Permettre à l'utilisateur de trier le catalogue la moyenne des notes |NON|
| 20 | Mot de passe oublié | Permettre à l'utilisateur de changer son mot de passe si celui-ci a été oublié via génération d'un token | NON |


## Répartition du travail 
Conception : Jasse & Ambroise
Base de donnée : Thomas
Structure du projet : Yanis & Louis

Une fois la conception terminée, nous avons travaillé sur les bases en répartissant mutuellement les tâches via les 'issues' sur github.

Chaque fonctionnalitée de base/étendue a été traitée par chaque membre du groupe (travail collaboratif)

### Les données 
les données utilisées se trouvent dans les fichiers : database/database.sql  ainsi  que  insertions.sql
