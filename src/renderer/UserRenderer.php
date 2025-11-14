<?php
declare(strict_types=1);
namespace netvod\renderer;

use netvod\auth\AuthzProvider;
use netvod\classes\User;

class UserRenderer implements Renderer {

    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

public function render(): string
    {
        $user = $this->user;

        // Sanitize fields
        $email = htmlspecialchars((string)($user->email ?? ''), ENT_QUOTES | ENT_SUBSTITUTE);
        $nom = htmlspecialchars((string)($user->nom ?? ''), ENT_QUOTES | ENT_SUBSTITUTE);
        $prenom = htmlspecialchars((string)($user->prenom ?? ''), ENT_QUOTES | ENT_SUBSTITUTE);

        // Verification block (if verified, show label, otherwise show a form to request a new token)
        if (AuthzProvider::isVerified()) {
            $verifBlock = '<p class="profile-verified" aria-live="polite">Vérifié</p>';
        } else {
            $verifBlock = <<<FIN
            <form method="post" action="?action=verify-mail" class="profile-verify-form" aria-label="Vérification de l'email">
                <p class="profile-not-verified" aria-live="polite">Non vérifié</p>
                <button type="submit" class="btn btn-primary">Générer un nouveau token de vérification</button>
            </form>
            FIN;
        }

        $infoItems = [];
        if ($nom !== '') $infoItems[] = "<dt>Nom</dt><dd>{$nom}</dd>";
        if ($prenom !== '') $infoItems[] = "<dt>Prénom</dt><dd>{$prenom}</dd>";
        $infoHtml = $infoItems ? '<dl class="profile-info">' . implode("\n", $infoItems) . '</dl>' : '<p class="profile-empty">Aucune information personnelle renseignée.</p>';

        return <<<FIN
        <section class="profile-page container">
            <header class="profile-header">
                <h1>Mon profil</h1>
                <p class="profile-email">{$email}</p>
            </header>

            <div class="profile-card">
                <div class="profile-main">
                {$verifBlock}

                <div class="profile-details">
                    {$infoHtml}
                </div>

                <div class="profile-actions">
                    <a class="btn btn-secondary" href="?action=profil-info">Modifier les informations</a>
                </div>
                </div>
            </div>
        </section>
        FIN;
    }

}