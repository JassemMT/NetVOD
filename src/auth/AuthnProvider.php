<?php
declare(strict_types=1);

namespace netvod\auth;

use netvod\exception\AuthnException;
use netvod\core\Database;

class AuthnProvider {

    public static function signin(string $email, string $password): void {
        $repo = Database::getInstance();

        $user = $repo->findUserByEmail($email); // user tab ou objet ?
        if (!$user) {
            throw new AuthnException('User not found');
        }

        $hash = $user['password'] ?? $user['passwd'] ?? null;
        if (!is_string($hash) || $hash === '' || !password_verify($password, $hash)) {
            throw new AuthnException('Invalid credentials');
        }
        // /!\ trop d'info pour l'attaquant

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        
    }

    public static function getSignedInUser(): array {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            throw new AuthnException('Aucun utilisateur authentifié');
        }
        // check dans la BD que l'user_id est valide (pas supprimé entre temps) ?
        return $_SESSION['user'];
    }

    public static function isLoggedIn(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ?action=signin');
            exit();
        }
    }

    public static function register(string $email, string $password): int
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthnException('Email invalide');
        }

        if (mb_strlen($password) < 10) {
            throw new AuthnException('Mot de passe trop court (min 10 caractères)');
        }

        $repo = DeefyRepository::getInstance();
        $existing = $repo->findUserByEmail($email);
        if ($existing) { // à completer avec un trigger pour être sûr qu'il n'y a pas de doublon
            throw new AuthnException('Un compte existe déjà pour cet email');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ["cost" => 12]); // ajout du coût
        if ($hash === false) {
            throw new AuthnException('Erreur de hachage du mot de passe');
        }

        $id = $repo->createUser($email, $hash, 1);// 1 pour le role ?
        if (isset($id) === false) { // ce qui peut être fait au lieu d'avoir un retour null c'est de lancer une exception dans la méthode createUser et de l'attraper ici
            throw new AuthnException('Erreur création utilisateur');
        }

        AuthnProvider::signin($email, $password);

        return (int)$id;
    }
}
