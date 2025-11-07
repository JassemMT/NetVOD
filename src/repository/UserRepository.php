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
    private static ?UserRepository $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        // Singleton géré par netvod/core/Database
        $this->pdo = Database::getInstance()->pdo;
    }

    public static function getInstance(): UserRepository
    {
        if (self::$instance === null) {
            self::$instance = new UserRepository();
        }
        return self::$instance;
    }

    /*
       AUTHENTIFICATION
    */
  
    public function findUserByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE mail = :mail');
        $stmt->execute(['mail' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) throw new \PDOException('Utilisateur non trouvé.');

        return new User( $data['mail'], (int)$data['id_user']);
    }

    public function getHash(string $email): string
    {
        $stmt = $this->pdo->prepare('SELECT password FROM user WHERE mail = :mail');
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

    public function createUser(string $email, string $passwordHash): User
    {
        $stmt = $this->pdo->prepare('INSERT INTO user (mail, password) VALUES (:mail, :password)');
        $stmt->execute(['mail' => $email, 'password' => $passwordHash]);

        $id = (int)$this->pdo->lastInsertId();
        return new User($email,$id);
    }

    /* 
       GESTION DES LISTES
     */

    public function getUserLists(int $id_user): array
    {
        return [
            'favoris' => $this->getFavoriteSeries($id_user),
            'en_cours' => $this->getInProgressSeries($id_user)
        ];
    }

    public function addSerieToList(int $id_user, int $id_serie, string $listName): bool
    {
        $table = match ($listName) {
            'favoris' => 'User2favori',
            'en_cours' => 'User2encours',
            default => throw new Exception('Liste inconnue'),
        };

        $stmt = $this->pdo->prepare("INSERT IGNORE INTO $table (id_user, id_serie) VALUES (:id_user, :id_serie)");
        return $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_serie]);
    }

    public function removeSerieFromList(int $id_user, int $id_serie, string $listName): bool
    {
        $table = match ($listName) {
            'favoris' => 'User2favori',
            'en_cours' => 'User2encours',
            default => throw new Exception('Liste inconnue'),
        };

        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id_user = :id_user AND id_serie = :id_serie");
        return $stmt->execute(['id_user' => $id_user, 'id_serie' => $id_serie]);
    }





}
