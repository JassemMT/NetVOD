<?php
declare(strict_types=1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class LoginFormRenderer implements Renderer {

    public function render(): string {
        return <<<FIN
                    <h1>Connexion</h1>
                    <form action="?action=login" method="post" class="auth-form">
                        <label for="mail">Adresse mail</label>
                        <input type="email" id="mail" placeholder="exemple@mail.com" name="mail" required>

                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" placeholder="••••••••" name="password" required>

                        <button type="submit" class="btn-primary">Se connecter</button>
                    </form>
                    FIN;
    }
}   