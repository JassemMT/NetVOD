<?php
declare(strict_types=1);

namespace netvod\repository;

use PDO;
use PDOException;
use netvod\core\Database;

class TokenRepository{
    public static function setToken(int $id_user, string $token, int $created_at, int $life_time) : void {
        if (!self::hasToken($id_user)) {
            $pdo = Database::getInstance()->pdo;
            $statement = $pdo->prepare("INSERT INTO tokens (id_user, token, created_at, life_time) VALUES (:id_user, :token, :created_at, :life_time)");
            $statement->execute([
                'id_user' => $id_user,
                'token' => $token,
                'created_at' => $created_at,
                'life_time' => $life_time
            ]);
        } else {
            $pdo = Database::getInstance()->pdo;
            $statement = $pdo->prepare("UPDATE tokens SET token = :token, created_at = :created_at, life_time = :life_time WHERE id_user = :id_user");
            $statement->execute([
                'id_user' => $id_user,
                'token' => $token,
                'created_at' => $created_at,
                'life_time' => $life_time
            ]);
        }
    }

    public static function hasToken(int $id_user) : bool {
        $pdo = Database::getInstance()->pdo;
        $statement = $pdo->prepare("SELECT COUNT(*) FROM tokens WHERE id_user = :id_user");
        $statement->execute(['id_user' => $id_user]);
        $count = (int) $statement->fetchColumn();
        return $count > 0;
    }

    public static function getToken(int $id_user) : ?array {
        $pdo = Database::getInstance()->pdo;
        $statement = $pdo->prepare("SELECT token, created_at, life_time FROM tokens WHERE id_user = :id_user");
        $statement->execute(['id_user' => $id_user]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function deleteToken(int $id_user) : void {
        $pdo = Database::getInstance()->pdo;
        $statement = $pdo->prepare("DELETE FROM tokens WHERE id_user = :id_user");
        $statement->execute(['id_user' => $id_user]);
    }
}