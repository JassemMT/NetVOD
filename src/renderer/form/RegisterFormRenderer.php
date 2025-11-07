<?php
declare(strict_types=1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class RegisterFormRenderer implements Renderer {
    public static function render(array $data = []): string {
        return <<<FIN
                <div class="auth-container">
                    <div class="auth-card">
                        <h1>Inscription</h1>
                        <p class="auth-subtitle">Créez votre compte</p>
                        
                        <form action="?action=register" method="post" class="auth-form">
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
    }
}