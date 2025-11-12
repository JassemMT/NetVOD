<?php
declare(strict_types=1);
namespace netvod\repository;

use netvod\core\Database;
use netvod\classes\Episode;

class EpisodeRepository extends Database {

    public static function findById(int $id_episode): ?Episode {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare('SELECT * FROM episode WHERE id_episode = :id_episode');
        $stmt->execute(['id_episode' => $id_episode]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            return new Episode(
                (int)$data['id_episode'],
                (int)$data['numero'],
                $data['titre'],
                'description', // pas présente dans la table episode
                (int)$data['duree'],
                $data['source'],
                $data['src_image']
            );
        } else {
            return null;
        }
    }

    public static function findBySerie(int $id_serie): array {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('SELECT * FROM episode WHERE id_serie = :id_serie ORDER BY numero ASC');
        $stmt->execute(['id_serie' => $id_serie]);
        $episodesData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($episodesData) {
            $episodes = [];
            foreach ($episodesData as $data) {
                $episodes[] = self::findById((int)$data['id_episode']);
            }
            return $episodes;
        } else {
            return [];
        }
    }


    public static function markAsWatching(int $id_user, int $id_serie): bool  { // ajoute à "en cours"
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare('INSERT INTO user2encours (id_user, id_serie) VALUES (:id_user, :id_serie)');
        try {
            $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_serie]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function markAsFavorite(int $id_user, int $id_serie): bool { // ajoute à "favoris"
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare('INSERT INTO user2favori (id_user, id_serie) VALUES (:id_user, :id_serie)');
        try {
            $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_serie]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function findSerieByID(int $id_episode): int {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare('SELECT id_serie FROM episode WHERE id_episode = :id_episode');
        $stmt->execute(['id_episode' => $id_episode]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) return (int)$data['id_serie'];
        else throw new \PDOException("Épisode introuvable dans la base de données.");
    }

    public static function estPremierEpisode(int $id_episode): bool {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare('SELECT numero FROM episode WHERE id_episode = :id_episode');
        $stmt->execute(['id_episode' => $id_episode]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            return ((int)$data['numero'] === 1);
        } else {
            throw new \PDOException("Épisode introuvable dans la base de données.");
        }
    }

    public static function estDernierEpisode(int $id_episode): bool {
        $pdo = Database::getInstance()->pdo;

        // Récupérer l'id_serie de l'épisode donné
        $stmt = $pdo->prepare('SELECT id_serie, numero FROM episode WHERE id_episode = :id_episode');
        $stmt->execute(['id_episode' => $id_episode]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            $id_serie = (int)$data['id_serie'];
            $numero_episode = (int)$data['numero'];

            // Récupérer le nombre total d'épisodes pour cette série
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM episode WHERE id_serie = :id_serie');
            $stmt->execute(['id_serie' => $id_serie]);
            $countData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($countData) {
                $total_episodes = (int)$countData['total'];
                return ($numero_episode === $total_episodes);
            } else {
                throw new \PDOException("Impossible de récupérer le nombre total d'épisodes.");
            }
        } else {
            throw new \PDOException("Épisode introuvable dans la base de données.");
        }
    }

}