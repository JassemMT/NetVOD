<?php
declare(strict_types=1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class LoginFormRenderer implements Renderer {

    public function render(): string {
        return <<<HTML
        <div class="auth-page">
            <div class="auth-container" role="main" aria-labelledby="auth-title">
                <header class="auth-header">
                <h1 id="auth-title">Connexion</h1>
                <p class="auth-sub">Connectez-vous pour accéder à votre espace</p>
                </header>

                <form action="?action=login" method="post" class="auth-form" novalidate>
                <div>
                    <label for="mail">Adresse mail</label>
                    <input id="mail" name="mail" type="email" placeholder="exemple@mail.com" required autocomplete="email" />
                </div>

                <div>
                    <label for="password">Mot de passe</label>
                    <div class="input-with-toggle">
                    <input id="password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password" />
                    <button class="toggle-pw" type="button" aria-pressed="false" aria-label="Afficher le mot de passe">Voir</button>
                    </div>
                </div>

                <div class="form-error" role="alert" aria-live="assertive"></div>

                <div class="auth-actions">
                    <div class="left">
                    <label class="form-help"><input type="checkbox" name="remember" /> Se souvenir</label>
                    </div>
                    <div class="right">
                    <a href="?action=forgot" class="link-muted">Mot de passe oublié ?</a>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        HTML;
    }
}   