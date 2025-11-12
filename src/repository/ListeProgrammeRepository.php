<?php
declare(strict_types=1);
namespace netvod\repository;

use netvod\core\Database;
use netvod\classes\Serie;
use netvod\classes\Episode;
use netvod\classes\ListeProgramme;
use PDO;

/*
findAllProgrammes() : SerieListe (avec toutes les series)

addProgrammeToFavoris(int $id_liste, int $id_programme): bool
addProgrammeToEnCours(int $id_liste, int $id_programme): bool
addProgrammeToList(int $id_liste, int $id_programme): bool //Est utilisé par addProgrammeToFavoris et addProgrammeToList

removeProgrammeFromList(int $id_liste, int $id_programme): bool

getFavoriteSeries(int $id_user): ListeProgramme 
getEnCoursSeries(int $id_user): ListeProgramme
fetchSeriesList(string $table, int $id_user): ListeProgramme //Est utilisé par getFavoriteSeries & getEnCoursSeries

*/


class ListeProgrammeRepository
{
    /* 
       LISTES GLOBALES
     */

    public static function findAllProgrammes(): ListeProgramme
    {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->query('SELECT * FROM serie ORDER BY titre ASC');
        $seriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $liste = new ListeProgramme('Toutes les séries');

        foreach ($seriesData as $s) {
            $serie = new Serie($s['titre'], $s['description'], (int)$s['annee'], $s['image']);
            self::chargerEpisodes($serie);
            $liste->ajouterProgramme($serie);
        }

        return $liste;
    }

    /* 
       AJOUT / SUPPRESSION DANS LES LISTES
     */

    public static function addProgrammeToFavoris(int $id_user, int $id_programme): bool
    {
        return self::addProgrammeToList('User2favori', $id_user, $id_programme);
    }

    public static function addProgrammeToEnCours(int $id_user, int $id_programme): bool
    {
        return self::addProgrammeToList('User2encours', $id_user, $id_programme);
    }

    public static function addProgrammeToList(string $table, int $id_user, int $id_programme): bool
    {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare("INSERT IGNORE INTO $table (id_user, id_serie) VALUES (:id_user, :id_serie)");
        return $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_programme]);
    }

    public static function removeProgrammeFromList(int $id_user, int $id_programme, string $table): bool
    {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare("DELETE FROM $table WHERE id_user = :id_user AND id_serie = :id_serie");
        return $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_programme]);
    }

    /* 
       LISTES PAR USEr
     */

    public static function getFavoriteSeries(int $id_user): ListeProgramme
    {
        return self::fetchSeriesList('User2favori', $id_user, 'Favoris');
    }

    public static function getEnCoursSeries(int $id_user): ListeProgramme
    {
        return self::fetchSeriesList('User2encours', $id_user, 'En cours');
    }

    public static function fetchSeriesList(string $table, int $id_user, string $nomListe = ''): ListeProgramme
    {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare(
            "SELECT s.* FROM serie s
             INNER JOIN $table u2s ON s.id_serie = u2s.id_serie
             WHERE u2s.id_user = :id_user
             ORDER BY s.titre ASC"
        );
        $stmt->execute(['id_user' => $id_user]);
        $seriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $liste = new ListeProgramme($nomListe ?: 'Liste');

        foreach ($seriesData as $s) {
            $serie = new Serie((int)$s['id_serie'], $s['titre'], $s['description'], (int)$s['annee'], $s['image']);
            self::chargerEpisodes($serie);
            $liste->ajouterProgramme($serie);
        }

        return $liste;
    }

    /* 
       CHARGER LES EPISODES
     */

    private static function chargerEpisodes(Serie $serie): void
    {
        $pdo = Database::getInstance()->pdo;

        // Récupère l'id de la série par titre (ou idéalement il faudrait stocker id_serie dans Serie)
        $stmtId = $pdo->prepare('SELECT id_serie FROM serie WHERE titre = :titre');
        $stmtId->execute(['titre' => $serie->getTitre()]);
        $idSerie = (int)$stmtId->fetchColumn();

        $stmtEp = $pdo->prepare('SELECT * FROM episode WHERE id_serie = :id_serie ORDER BY numero ASC');
        $stmtEp->execute(['id_serie' => $idSerie]);
        $episodesData = $stmtEp->fetchAll(PDO::FETCH_ASSOC);

        foreach ($episodesData as $ep) {
            $episode = new Episode(
                (int)$ep['id_episode'],
                (int)$ep['numero'],
                $ep['titre'],
                'descritpion', // Description non présente dans la table episode
                (int)$ep['duree'],
                $ep['source'],
                $ep['src_image']
            );
            $serie->ajouterEpisode($episode);
        }
    }
}




