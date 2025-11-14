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

    public static function findAll(?string $genre = null, ?string $public = null): ListeProgramme {
    $pdo = Database::getInstance()->pdo;

    $sql = "SELECT id_serie, titre, description, annee, image, genre, public
            FROM serie";
    $params = [];
    $conditions = [];

    // Ajout dynamique des filtres
    if ($genre !== null && $genre !== '') {
        $conditions[] = "genre = :genre";
        $params['genre'] = $genre;
    }

    if ($public !== null && $public !== '') {
        $conditions[] = "`public` = :public";
        $params['public'] = $public;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY titre";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $listeProgramme = new \netvod\classes\ListeProgramme("Catalogue des séries");
    $series = $stmt->fetchAll();

    foreach ($series as $s) {
        $serie = new \netvod\classes\Serie(
            (int)$s['id_serie'],
            $s['titre'],
            $s['description'],
            (int)$s['annee'],
            $s['image'],
            $s['genre'] ?? '',
            $s['public'] ?? ''
        );
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

        $serie = new Serie((int)$s['id_serie'], $s['titre'], $s['description'], (int)$s['annee'], $s['image']);
        $episodes = EpisodeRepository::findBySerie((int)$s['id_serie']);
        foreach ($episodes as $ep) {
            $serie->ajouterEpisode($ep);
        }

        return $serie;
    }
    public static function search(string $keywords): ListeProgramme {
    $pdo = Database::getInstance()->pdo;

    $sql = "SELECT * FROM serie 
            WHERE titre LIKE :kw ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':kw' => '%' . $keywords . '%'
    ]);

    $rows = $stmt->fetchAll();

    // On crée un objet ListeProgramme
    $liste = new ListeProgramme("Résultats de recherche");

    foreach ($rows as $s) {
        $serie = new Serie(
            (int)$s['id_serie'],
            $s['titre'],
            $s['description'],
            (int)$s['annee'],
            $s['image']
        );

        // On ajoute la série dans la ListeProgramme
        $liste->ajouterProgramme($serie);
    }

    return $liste;
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

    public static function getAverageRating(int $id_serie):float{
        $pdo = Database::getInstance()->pdo;
        $sql = "
                SELECT 
                    AVG(note) AS moyenne_notes
                FROM commentaire
                WHERE id_serie = :id_serie
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
    public static function findAllGenres(): array {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->query("SELECT DISTINCT genre FROM serie WHERE genre IS NOT NULL ORDER BY genre");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function findAllPublics(): array {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->query("SELECT DISTINCT `public` FROM serie WHERE `public` IS NOT NULL ORDER BY `public`");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function isFavoris(int $id_user, int $id_serie): bool {
        $pdo = Database::getInstance()->pdo;
        $sql = "SELECT 1 FROM user2favori WHERE id_user = :id_user AND id_serie = :id_serie LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_user' => $id_user,
            ':id_serie' => $id_serie
        ]);

        return $stmt->fetch() !== false;
    }


}