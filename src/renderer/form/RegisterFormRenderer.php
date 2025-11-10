<?php
declare(strict_types=1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class RegisterFormRenderer implements Renderer {
    public function render(): string {
        return <<<HTML
        <div class="auth-page">
            <div class="auth-container" role="main" aria-labelledby="auth-title">
                <div class="auth-card" aria-live="polite">
                <header class="auth-header">
                    <h1 id="auth-title">Inscription</h1>
                    <p class="auth-sub">Créez votre compte</p>
                </header>

                <form action="?action=register" method="post" class="auth-form" novalidate>
                    <div class="form-group">
                    <label for="mail">Adresse mail</label>
                    <input id="mail" name="mail" type="email" placeholder="exemple@mail.com"
                            required autocomplete="email" />
                    </div>

                    <div class="form-group">
                    <label for="password1">Mot de passe</label>
                    <div class="input-with-toggle">
                        <input id="password1" name="password1" type="password" required autocomplete="new-password" />
                        <button class="toggle-pw" type="button" aria-pressed="false" aria-label="Afficher le mot de passe">Voir</button>
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="password2">Confirmation</label>
                    <div class="input-with-toggle">
                        <input id="password2" name="password2" type="password" required autocomplete="new-password" />
                        <button class="toggle-pw" type="button" aria-pressed="false" aria-label="Afficher le mot de passe de confirmation">Voir</button>
                    </div>
                    </div>

                    <div class="auth-actions" style="margin-top:var(--spacing-md);">
                        <a href="?action=login" class="link-muted">Déjà un compte ? Se connecter</a>
                        <button type="submit" class="btn btn-primary" style="margin-left:12px;">Créer mon compte</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        HTML;
    }
}