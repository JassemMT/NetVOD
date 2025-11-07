<?php
declare(strict_types=1);
namespace netvod\repository;

use netvod\core\Database;
use netvod\classes\Episode;

class EpisodeRepository extends Database {

    public static function findById(int $id_episode): ?Episode {
        $pdo = Database::getInstance()->pdo;

        $stmt = $pdo->prepare('SELECT * FROM episode WHERE id = :id_episode');
        $stmt->execute(['id_episode' => $id_episode]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            return new Episode(
                (int)$data['id_episode'],
                (int)$data['numero'],
                $data['titre'],
                'description', // pas présente dans la table episode
                (int)$data['duree'],
                (int)$data['source'],
                (int)$data['src_image']
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

}
