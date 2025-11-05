<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\BadRequestMethodException;
use netvod\repository\UserRepository;
use netvod\exception\AuthnException;

class LogInAction implements Action {

    public function execute() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<FIN
                    <h1>Connexion</h1>
                    <form action="?action=lognin" method="post" class="auth-form">
                        <label for="mail">Adresse mail</label>
                        <input type="email" id="mail" placeholder="exemple@mail.com" name="mail" required>

                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" placeholder="••••••••" name="password" required>

                        <button type="submit" class="btn-primary">Se connecter</button>
                    </form>
                    FIN;
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification des champs
            if (!isset($_POST['mail'])) throw new MissingArgumentException("email");
            if (!isset($_POST['password'])) throw new MissingArgumentException("password");

            $mail = trim((string)$_POST['mail']);
            $password = (string)$_POST['password'];

            $mail = filter_var($mail, FILTER_SANITIZE_EMAIL);
            $password = filter_var($password, FILTER_SANITIZE_STRING);

            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("email");
            }

            // Utilise AuthnProvider::login pour vérifier et créer la session
            try {
                AuthnProvider::login($mail, $password);
                return "connection réussie";
            } catch (AuthnException $e) {
                throw new InvalidArgumentException('credentials');
            } catch (\Exception $e) {
                throw new \RuntimeException('Erreur lors de l\'authentification');
            }

        } else throw new BadRequestMethodException();
    }    
}