<?php
declare(strict_types=1);
namespace iutnc\deefy\action;

use netvod\auth\AuthnProvider;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\BadRequestMethodException;

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
            if (isset($_POST['mail'])) {
                if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL) !== null) {
                    $mail = $_POST['mail'];
                    AuthnProvider::signin($mail, $_POST['password']);
                    return "";
                } else throw new InvalidArgumentException("email");
            } else throw new MissingArgumentException("email");
        } else throw new BadRequestMethodException();
    }    
}