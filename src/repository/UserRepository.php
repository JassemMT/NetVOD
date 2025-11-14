<?php
declare(strict_types=1);

namespace netvod\repository;

use netvod\core\Database;
use netvod\classes\User;
use netvod\exception\AuthnException;

use netvod\action\LogInAction;

use PDO;
use PDOException;
use Exception;

/*
//liste des méthodes à programmer

// Authentification
findUserByEmail(string $email): ?User  //Throws Exception
verifyCredentials(string $email, string $password): ?User
getHash(string $email) : string //Throws Exception 

createUser(string $email, string $passwordHash): User

// Gestion des listes / préférences
getUserLists(int $id_user): array
addSerieToList(int $id_user, int $id_serie, string $listName): bool
removeSerieFromList(int $id_user, int $id_serie, string $listName): bool
getFavoriteSeries(int $id_user): array (objet Series)
getInProgressSeries(int $id_user): array (objet Series)

*/

class UserRepository
{ 
    public static function Email2Id(string $email): int // TODO : trigger pour l'unicité de l'email
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('SELECT * FROM user WHERE mail = :mail');
        $stmt->execute(['mail' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) throw new \PDOException('Utilisateur non trouvé.');

        return (int)$data['id_user'];
    }

    public static function getUserById(int $id_user): User
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('SELECT * FROM user WHERE id_user = :id_user');
        $stmt->execute(['id_user' => $id_user]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) throw new \PDOException('Utilisateur non trouvé.');

        return new User($data['mail'], (int)$data['id_user'], $data['nom'], $data['prenom']);
    }

    public static function getHash(string $email): string
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('SELECT password FROM user WHERE mail = :mail');
        $stmt->execute(['mail' => $email]);
        $hash = $stmt->fetchColumn();

        if (!$hash) {
            throw new AuthnException('Utilisateur introuvable.');
        }

        return (string)$hash;
    }
/* Inutile à cause de AuthnProvider::login

    public function verifyCredentials(string $email, string $password): ?User
    {
        $hash = $this->getHash($email);
        if (password_verify($password, $hash)) {
            return $this->findUserByEmail($email);
        }
        return null;
    }
*/

    public static function createUser(string $email, string $passwordHash): int
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('INSERT INTO user (mail, password) VALUES (:mail, :password)');
        $stmt->execute(['mail' => $email, 'password' => $passwordHash]);

        $id = (int)$pdo->lastInsertId();
        return $id;
    }


    public static function getUserLists(int $id_user): array
    {
        return [
            'favoris' => ListeProgrammeRepository::getFavoriteSeries($id_user),
            'en_cours' => ListeProgrammeRepository::getEnCoursSeries($id_user)
        ];
    }


    public static function addSerieToList(int $id_user, int $id_serie, string $listName): bool
    {
        $table = match ($listName) {
            'favoris' => 'User2favori',
            'en_cours' => 'User2encours',
            default => throw new \PDOException('Liste inconnue'),
        };
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare("INSERT IGNORE INTO $table (id_user, id_serie) VALUES (:id_user, :id_serie)");
        return $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_serie]);
    }

    public static function removeSerieFromList(int $id_user, int $id_serie, string $listName): bool
    {
        $table = match ($listName) {
            'favoris' => 'User2favori',
            'en_cours' => 'User2encours',
            default => throw new Exception('Liste inconnue'),
        };
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id_user = :id_user AND id_serie = :id_serie");
        return $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_serie]);
    }


    public static function getVerifier(int $id_user): bool  //TODO : ajouter le champ verified dans la table user
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('SELECT verified FROM user WHERE id_user = :id_user');
        $stmt->execute(['id_user' => $id_user]);
        $verified = $stmt->fetchColumn();

        if ($verified === false) {
            throw new \PDOException('Utilisateur introuvable.');
        }

        return (bool)$verified;
    }

    public static function setVerifier(int $id_user, bool $status): void
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('UPDATE user SET verified = :verified WHERE id_user = :id_user');
        $stmt->execute(['verified' => $status, 'id_user' => $id_user]);
    }

    public static function updateProfilInfo(int $id_user, string $nom, string $prenom): void
    {
        $pdo = Database::getInstance()->pdo;
        $stmt = $pdo->prepare('UPDATE user SET nom = :nom, prenom = :prenom WHERE id_user = :id_user');
        $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'id_user' => $id_user]);
    }




}
