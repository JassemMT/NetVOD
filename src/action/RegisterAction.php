<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\repository\UserRepository;

class RegisterAction implements Action {

    public function execute() : string {
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
                                    <input type="password" id="password1" placeholder="••••••••" name="password1" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Confirmation</label>
                                    <input type="password" id="password2" placeholder="••••••••" name="password2" required>
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
            // Vérification des champs obligatoires
            if (!isset($_POST['mail'])) throw new MissingArgumentException("email");
            if (!isset($_POST['password1']) || !isset($_POST['password2'])) throw new MissingArgumentException("password");

            $mail = trim((string)($_POST['mail'] ?? ''));
            $pswd1 = (string)($_POST['password1'] ?? '');
            $pswd2 = (string)($_POST['password2'] ?? '');

            if (filter_var($mail, FILTER_VALIDATE_EMAIL) === false) {
                throw new InvalidArgumentException("email");
            }

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
            $existing = $repo->findUserByEmail($mail);
            if ($existing !== null) {
                throw new InvalidArgumentException("email");
            }

            $hash = password_hash($pswd1, PASSWORD_DEFAULT, ['cost' => 12]);
            if ($hash === false) {
                throw new \RuntimeException('Erreur de hachage du mot de passe');
            }

            try {
                $user = $repo->createUser($mail, $hash);

                // Initialise la session si nécessaire et stocke l'utilisateur
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                $_SESSION['user'] = $user;

                return '';
            } catch (\Exception $e) {
                throw new \RuntimeException('Erreur création utilisateur');
            }

        } else throw new BadRequestMethodException();
    }
}