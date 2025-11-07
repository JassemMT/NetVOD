<?php
declare(strict_types=1);

namespace netvod\repository;

use netvod\core\Database;
use netvod\model\User;
use netvod\exception\AuthnException;

use netvod\action\LogInAction;

use PDO;
use PDOException;
use Exception;

class SerieRepository{
    private static ?SerieRepository $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        // Singleton géré par netvod/core/Database
        $this->pdo = Database::getInstance()->pdo;
    }

    public static function getInstance(): SerieRepository
    {
        if (self::$instance === null) {
            self::$instance = new SerieRepository();
        }
        return self::$instance;
    }

    public function findAll():array{
        $sql = "SELECT titre, description, annee, image FROM serie ORDER BY titre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Récupérer toutes les lignes
        $series = $stmt->fetchAll();
        $lSerie = [];
        foreach ($series as $s) {

            // mettre la liste de serie en Session?
            // car les series ne seront accèssible que dans 
            $seri = new Serie($s['titre'], $s['description'], (int)$s['annee'], $s['image']);
            $lSerie.array_push($seri);

        }
        return $lSerie;
    }

    public function findById(int $id_serie):Serie {
        $sql = " SELECT titre, description, annee, image FROM serie WHERE id_serie=:idSerie  ORDER BY titre  ";
        $stmt = $pdo->prepare(['idSerie'=>$id_serie]);
        $stmt->execute();
        $serie = $stmt->fetchAll();

        $lSerie = [];
        foreach ($serie as $s) {

            // mettre la liste de serie en Session?
            // car les series ne seront accèssible que dans 
            $seri = new Serie($s['titre'], $s['description'], (int)$s['annee'], $s['image']);
            $lSerie.array_push($seri);

        }
        return $lSerie;
    }

    public function findByTitle(string $titre):Serie{
        $sql = " SELECT titre, description, annee, image FROM serie WHERE titre=:titre";
        $stmt = $pdo->prepare(['titre'=>$titre]);
        $stmt->execute();
        $serie = $stmt->fetchAll();

        foreach ($serie as $s) {

            // mettre la liste de serie en Session?
            // car les series ne seront accèssible que dans 
            $seri = new Serie($s['titre'], $s['description'], (int)$s['annee'], $s['image']);
        }
        return $seri;
    }

    public function getAverageRating(int $id_serie):float{
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

    public function addComment(int $id_user, int $id_serie, int $note, string $contenu): bool {
        if ($note < 0 || $note > 5) {
            throw new InvalidArgumentException("La note doit être entre 0 et 5.");
        }

        if (empty($id_user) || empty($id_serie)) {
            throw new InvalidArgumentException("ID utilisateur et série requis.");
        }
        if (!empty(trim($contenu))) {
            $sql = "INSERT INTO commentaire (id_user, id_serie, note, contenu) 
                    VALUES (:id_user, :id_serie, :note, :contenu)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_user'   => $id_user,
                ':id_serie'  => $id_serie,
                ':note'      => $note,
                ':contenu'   => $contenu
            ]);
            return true;
            
        } else throw new InvalidArgumentException("Impossible d'insérer un commentaire vide");
    }

    public function updateComment(int $id_user, int $id_serie, int $note, string $contenu): bool {
        // 1. Validation des données
        if ($note < 0 || $note > 5) {
            throw new InvalidArgumentException("La note doit être entre 0 et 5.");
        }

        if (empty($id_user) || empty($id_serie)) {
            throw new InvalidArgumentException("ID utilisateur et série requis.");
        }

        $contenu = trim($contenu);
        if (empty($contenu)) {
            throw new InvalidArgumentException("Le commentaire ne peut pas être vide.");
        }

        // 2. Requête préparée : mise à jour
        $sql = "UPDATE commentaire 
                SET note = :note, 
                    contenu = :contenu, 
                    date = CURRENT_TIMESTAMP 
                WHERE id_user = :id_user 
                AND id_serie = :id_serie";

        $stmt = $this->pdo->prepare($sql);

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

    public function hasUserCommented(int $id_user, int $id_serie): bool {
        // 1. Vérification de la présence d'un userID et sérieID
        if (empty($id_user) || empty($id_serie)) {
            throw new InvalidArgumentException("ID utilisateur et série requis.");
        }

        // 2. Requête préparée : vérification de l'existence du commentaire récherché 
        $sql = "SELECT 1 
                FROM commentaire 
                WHERE id_user = :id_user 
                AND id_serie = :id_serie 
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        // 3. Exécution de la requête préparée
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
