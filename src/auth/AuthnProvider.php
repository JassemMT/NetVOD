<?php
declare(strict_types=1);

namespace netvod\auth;

use netvod\exception\AuthnException;
use Exception;

use netvod\repository\UserRepository;

class AuthnProvider {



    public static function login(string $email, string $password): void {
        try {
            // Vérifie que l'utilisateur existe
            $user = UserRepository::findUserByEmail($email);

            if (!$user) {
                throw new AuthnException('Email ou mot de passe incorrect');
            }

            // Récupère le hash du mot de passe depuis la base
            $hash = UserRepository::getHash($email);
            if (!is_string($hash) || $hash === '' || !password_verify($password, $hash)) {
                throw new AuthnException('Email ou mot de passe incorrect');
            }

            $_SESSION['user'] = $user;

        } catch (Exception $e) {
            throw new AuthnException('Erreur d’authentification');
        }
    }


   public static function getSignedInUser(): User {
    if (!isset($_SESSION['user'])) {
        throw new AuthnException('Aucun utilisateur authentifié');
    }

    $user = $_SESSION['user'];

    // Vérifie que l'utilisateur existe encore
    $check = UserRepository::findUserByEmail($user->email);
    if (!$check) {
        session_destroy();
        throw new AuthnException('Utilisateur introuvable');
    }

    return $user;
}


    public static function isLoggedIn(): bool {
        return isset($_SESSION['user']);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ?action=signin');
            exit();
        }
    }

    public static function register(string $email, string $password): void {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthnException('Email invalide');
        }

        if (mb_strlen($password) < 10) {
            throw new AuthnException('Mot de passe trop court (min 10 caractères)');
        }

        $existing = UserRepository::findUserByEmail($email);
        if ($existing) {
            throw new AuthnException('Un compte existe déjà pour cet email');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        if ($hash === false) {
            throw new AuthnException('Erreur de hachage du mot de passe');
        }

        // createUser retourne maintenant un User
        $user = UserRepository::createUser($email, $hash);
        if (!$user instanceof User) {
            throw new AuthnException('Erreur création utilisateur');
        }

        $_SESSION['user'] = $user;
        //pas besoin de retourner un utilisateur s'il est mis en session ici
    }

}

