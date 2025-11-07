<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\form\LoginFormRenderer;
use netvod\repository\UserRepository;
use netvod\exception\AuthnException;

class LogInAction implements Action {

    public function execute() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return LoginFormRenderer::render();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification des champs
            if (!isset($_POST['mail'])) throw new MissingArgumentException("email");
            if (!isset($_POST['password'])) throw new MissingArgumentException("password");

            $mail = trim((string)$_POST['mail']);
            $password = (string)$_POST['password'];

            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("email");
            }

            // Utilise le UserRepository pour vérifier les identifiants
            $repo = UserRepository::getInstance();
            try {
                $user = $repo->verifyCredentials($mail, $password);
                if ($user === null) {
                    // Identifiants invalides
                    throw new AuthnException('Email ou mot de passe incorrect');
                }

                // Initialise la session si nécessaire et stocke l'utilisateur
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                $_SESSION['user'] = $user;

                // Retour vide — la redirection est gérée ailleurs (Dispatcher)
                return "";
            } catch (AuthnException $e) {
                // Remonter comme InvalidArgument pour conserver la sémantique des actions
                throw new InvalidArgumentException('credentials');
            } catch (\Exception $e) {
                // Erreur serveur inattendue
                throw new \RuntimeException('Erreur lors de l\'authentification');
            }

        } else throw new BadRequestMethodException();
    }    
}