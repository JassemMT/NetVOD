<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;

class SignInAction implements Action {

    public static function execute() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<FIN
                    <div class="auth-container">
                        <div class="auth-card">
                            <h1>Inscription</h1>
                            <p class="auth-subtitle">Créez votre compte</p>
                            
                            <form action="?action=signup" method="post" class="auth-form">
                                <div class="form-group">
                                    <label for="mail">Adresse mail</label>
                                    <input type="email" id="mail" placeholder="exemple@mail.com" name="mail" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Mot de passe</label>
                                    <input type="password" id="password" placeholder="••••••••" name="password" required>
                                </div>

                                <button type="submit" class="btn-primary">Créer mon compte</button>
                            </form>
                            
                            <p class="auth-footer">
                                Déjà un compte ? <a href="?action=signin">Se connecter</a>
                            </p>
                        </div>
                    </div>
                    FIN;
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['mail'])) {
                if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL) !== null) {
                    if (isset($_POST['password'])){
                        if (!empty($_POST['password'])) {
                            $mail = $_POST['mail'];
                            $pswd = $_POST['password'];
                            AuthnProvider::register($mail, $pswd);
                            return '';
                        } else throw new InvalidArgumentException("password");
                    } else throw new MissingArgumentException("password");
                } else throw new InvalidArgumentException("email");
            } else throw new MissingArgumentException("email");
        } else throw new BadRequestMethodException();
    }    
}