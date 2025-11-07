<?php
declare(strict_types=1);

namespace netvod\repository;

use netvod\repository\EpisodeRepository;
use netvod\classes\ListeProgramme;
use netvod\classes\Serie;
use netvod\core\Database;
use netvod\model\User;
use netvod\exception\AuthnException;
use netvod\exception\InvalidArgumentException;
use netvod\action\LogInAction;

use PDO;
use PDOException;
use Exception;

class SerieRepository{

    public static function findAll():ListeProgramme{
        $pdo = Database::getInstance()->pdo;
        $sql = "SELECT id_serie, titre, description, annee, image FROM serie ORDER BY titre";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Récupérer toutes les lignes
        $listeProgramme = new ListeProgramme("Catalogue des séries");
        $series = $stmt->fetchAll();

        foreach ($series as $s) {

            // mettre la liste de serie en Session?
            // car les series ne seront accèssible que dans 
            $serie = new Serie((int)$s['id_serie'],$s['titre'], $s['description'], (int)$s['annee'], $s['image']);
            $listeProgramme->ajouterProgramme($serie);


        }
        return $listeProgramme;
    }

    public static function findById(string $id):Serie{
        $pdo = Database::getInstance()->pdo;
        $sql = " SELECT id_serie, titre, description, annee, image FROM serie WHERE id_serie=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id'=>$id]);
        $s = $stmt->fetch();

        // mettre la liste de serie en Session?
        // car les series ne seront accèssible que dans 
        $serie = new Serie((int)$s['id_serie'], $s['titre'], $s['description'], (int)$s['annee'], $s['image']);
        $episodes = EpisodeRepository::findBySerie((int)$s['id_serie']);
        foreach ($episodes as $ep) {
            $serie->ajouterEpisode($ep);
        }

        return $serie;
    }

    /*
    public function findByTitle(string $titre):Serie{
        $sql = " SELECT id_serie, titre, description, annee, image FROM serie WHERE titre=:titre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['titre'=>$titre]);
        $s = $stmt->fetch();

        // mettre la liste de serie en Session?
        // car les series ne seront accèssible que dans 
        $serie = new Serie($s['id_serie'], $s['titre'], $s['description'], (int)$s['annee'], $s['image']);
        $episodes = EpisodeRepository::findBySerie((int)$s['id_serie']);
        foreach ($episodes as $ep) {
            $serie->ajouterEpisode($ep);
        }

        return $serie;
    }
    */ // /!\ titre non unique, ne peut pas être utilisé pour identifier une série

    public function getAverageRating(int $id_serie):float{
        $pdo = Database::getInstance()->pdo;
        $sql = "
                SELECT 
                    AVG(note) AS moyenne_notes
                FROM commentaire
                WHERE id_serie = :idSerie
                AND note IS NOT NULL
            ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_serie'=>$id_serie]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $moyenne = $result['moyenne_notes'] ? $result['moyenne_notes'] : null;

        return (float)$moyenne;

    }

    public function getComments(int $id_serie):array{
        $pdo = Database::getInstance()->pdo;
        $sql = "
                SELECT 
                    c.id_commentaire,
                    c.id_user,
                    c.note,
                    c.contenu,
                    c.date,
                FROM commentaire c
                WHERE c.id_serie = :idSerie
                ORDER BY c.date DESC
            ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['idSerie'=>$id_serie]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addComment(int $id_user, int $id_serie, int $note, string $contenu): bool {
        if ($note < 0 || $note > 5) {
            throw new InvalidArgumentException("La note doit être entre 0 et 5."); // doit être vérifié par un trigger mysql
        }

        if (empty($id_user) || empty($id_serie)) {
            throw new InvalidArgumentException("ID utilisateur et série requis."); // doit être vérifié par un trigger mysql
        }
        if (!empty(trim($contenu))) {
            $pdo = Database::getInstance()->pdo;
            $sql = 'INSERT INTO commentaire (id_user, id_serie, note, contenu) 
                    VALUES (:id_user, :id_serie, :note, :contenu)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                    ':id_user'   => $id_user,
                    ':id_serie'  => $id_serie,
                    ':note'      => $note,
                    ':contenu'   => $contenu
            ]);
            return true;

        } else throw new InvalidArgumentException("Impossible d'insérer un commentaire vide");
    }

    public static function updateComment(int $id_user, int $id_serie, int $note, string $contenu): bool {
        // 1. Validation des données
        if ($note < 0 || $note > 5) {
            throw new InvalidArgumentException("La note doit être entre 0 et 5."); // doit être vérifié par un trigger mysql
        }

        if (empty($id_user) || empty($id_serie)) {
            throw new InvalidArgumentException("ID utilisateur et série requis.");// doit être vérifié par un trigger mysql
        }

        $contenu = trim($contenu);
        if (empty($contenu)) {
            throw new InvalidArgumentException("Le commentaire ne peut pas être vide.");// doit être vérifié par un trigger mysql
        }
        $pdo = Database::getInstance()->pdo;
        // 2. Requête préparée : mise à jour
        $sql = "UPDATE commentaire 
                SET note = :note, 
                    contenu = :contenu, 
                    date = CURRENT_TIMESTAMP 
                WHERE id_user = :id_user 
                AND id_serie = :id_serie";

        $stmt = $pdo->prepare($sql);

        // 3. Exécution
        $success = $stmt->execute([
                ':note'      => $note,
                ':contenu'   => $contenu,
                ':id_user'   => $id_user,
                ':id_serie'  => $id_serie
        ]);

        if (!$success) {
            return false;
        } else{
            return true;
        }

    }

    public static function hasUserCommented(int $id_user, int $id_serie): bool {
        //Vérification de la présence d'un userID et sérieID
        if (empty($id_user) || empty($id_serie)) {
            throw new InvalidArgumentException("ID utilisateur et série requis."); // doit être vérifié par un trigger mysql
        }

        $pdo = Database::getInstance()->pdo;
        //Requête préparée : vérification de l'existence du commentaire récherché
        $sql = "SELECT 1 
                FROM commentaire 
                WHERE id_user = :id_user 
                AND id_serie = :id_serie 
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        //Exécution de la requête préparée
        $success = $stmt->execute([
                ':id_user'  => $id_user,
                ':id_serie' => $id_serie
        ]);

        if (!$success) {
            return false;
        } else{
            // Permet de vérifier l'existence de la ligne contenant le commentaire
            return $stmt->fetchColumn() !== false;
        }
    }

}