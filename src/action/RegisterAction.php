<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\notification\Notification;
use netvod\repository\UserRepository;
use netvod\renderer\form\RegisterFormRenderer;
use netvod\token\TokenManager;

class RegisterAction implements Action {

    public function execute() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $renderer = new RegisterFormRenderer();
            return $renderer->render();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification des champs obligatoires
            if (!isset($_POST['mail'])) throw new MissingArgumentException("email");
            if (!isset($_POST['password1']) || !isset($_POST['password2'])) throw new MissingArgumentException("password");
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException("l'argument 'email' est invalide");
            
            $mail = (string)$_POST['mail'];
            $pswd1 = (string)$_POST['password1'];
            $pswd2 = (string)$_POST['password2'];
            
            if ($pswd1 === '' || $pswd2 === '') {
                throw new InvalidArgumentException("le mot de passe ne peut pas être vide");
            }

            if ($pswd1 !== $pswd2) {
                throw new InvalidArgumentException("les mots de passe ne correspondent pas");
            }

            if (mb_strlen($pswd1) < 10) {
                throw new InvalidArgumentException("le mot de passe doit contenir au moins 10 caractères");
            }
            
            AuthnProvider::register($mail, $pswd1); // a ce stade pswd1 et pswd2 sont identiques

            Notification::save('Inscription réussie, vous pouvez maintenant vous connecter.', "Succès", Notification::TYPE_SUCCESS);
            //header('Location: ?action=login');
            return <<<FIN
            <form method="post" action="?action=verify-mail" class="profile-verify-form" aria-label="Vérification de l'email">
                <button type="submit" class="btn btn-primary">vérifier son compte</button>
            </form>
            FIN;
        } else throw new BadRequestMethodException();
    }
}