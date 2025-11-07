<?php
declare(strict_types=1);

namespace netvod\auth;

use netvod\exception\AuthnException;
use Exception;

use netvod\repository\UserRepository;

class AuthnProvider {



    public static function login(string $email, string $password): void {
        $repo = UserRepository::getInstance();

        try {
            // Vérifie que l'utilisateur existe
            $user = $repo->findUserByEmail($email);

            if (!$user) {
                throw new AuthnException('Email ou mot de passe incorrect');
            }

            // Récupère le hash du mot de passe depuis la base
            $hash = $repo->getHash($email);
            if (!is_string($hash) || $hash === '' || !password_verify($password, $hash)) {
                throw new AuthnException('Email ou mot de passe incorrect');
            }

            // Initialise la session si nécessaire
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $_SESSION['user'] = $user;

        } catch (Exception $e) {
            throw new AuthnException('Erreur d’authentification');
        }
    }


   public static function getSignedInUser(): User {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['user'])) {
        throw new AuthnException('Aucun utilisateur authentifié');
    }

    $repo = UserRepository::getInstance();
    $user = $_SESSION['user'];

    // Vérifie que l'utilisateur existe encore
    $check = $repo->findUserByEmail($user->email);
    if (!$check) {
        session_destroy();
        throw new AuthnException('Utilisateur introuvable');
    }

    return $user;
}


    public static function isLoggedIn(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
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

        $repo = UserRepository::getInstance();
        $existing = $repo->findUserByEmail($email);
        if ($existing) {
            throw new AuthnException('Un compte existe déjà pour cet email');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        if ($hash === false) {
            throw new AuthnException('Erreur de hachage du mot de passe');
        }

        // createUser retourne maintenant un User
        $user = $repo->createUser($email, $hash);
        if (!$user instanceof User) {
            throw new AuthnException('Erreur création utilisateur');
        }

        $_SESSION['user'] = $user;
        //pas besoin de retourner un utilisateur s'il est mis en session ici
    }

}

