<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\BadRequestMethodException;
use netvod\notification\Notification;
use netvod\renderer\form\LoginFormRenderer;
use netvod\repository\UserRepository;
use netvod\exception\AuthnException;

class LogInAction implements Action {

    public function execute() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $renderer = new LoginFormRenderer();
            return $renderer->render();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification des champs
            if (!isset($_POST['mail'])) throw new MissingArgumentException("email");
            if (!isset($_POST['password'])) throw new MissingArgumentException("password");
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException("email");

            $mail = (string)$_POST['mail'];
            $password = (string)$_POST['password'];

            // Utilise AuthnProvider::login pour vérifier et créer la session
            try {
                AuthnProvider::login($mail, $password);
                header('Location: ?action=default');
                Notification::save('Connexion réussie',"Succès",  Notification::TYPE_SUCCESS);
                return "";
            } catch (AuthnException $e) {
                throw new InvalidArgumentException('credentials');
            }
            
        } else throw new BadRequestMethodException();
    }    
}