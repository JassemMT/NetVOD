<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\repository\UserRepository;
use netvod\renderer\form\RegisterFormRenderer;

class RegisterAction implements Action {

    public function execute() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $renderer = new RegisterFormRenderer();
            return $renderer->render();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification des champs obligatoires
            if (!isset($_POST['mail'])) throw new MissingArgumentException("email");
            if (!isset($_POST['password1']) || !isset($_POST['password2'])) throw new MissingArgumentException("password");
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException("email");
            
            $mail = (string)$_POST['mail'];
            $pswd1 = (string)$_POST['password1'];
            $pswd2 = (string)$_POST['password2'];
            
            if ($pswd1 === '' || $pswd2 === '') {
                throw new InvalidArgumentException("password");
            }

            if ($pswd1 !== $pswd2) {
                throw new InvalidArgumentException("password");
            }

            if (mb_strlen($pswd1) < 10) {
                throw new InvalidArgumentException("password");
            }

            // Utilise le UserRepository pour la création de l'utilisateur
            $repo = UserRepository::getInstance();

            // Vérifie qu'il n'existe pas déjà un utilisateur avec cet email
            try {
                $existing = $repo->findUserByEmail($mail);
                throw new InvalidArgumentException("email existe déjà");
            } catch (\PDOException $e) {
                // si on a une exception c'est que l'utilisateur n'existe pas, on peut continuer 
            }


            $hash = password_hash($pswd1, PASSWORD_DEFAULT, ['cost' => 12]);
            
            $user = $repo->createUser($mail, $hash);
            $_SESSION['user'] = $user;
            return "";
        } else throw new BadRequestMethodException();
    }
}