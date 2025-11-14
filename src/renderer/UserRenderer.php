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

    public function render(): string {
        $user = $this->user;
        $verif = AuthzProvider::isVerified()? "<p>Vérifié</p>" : "<p>Non vérifié</p>\n <form method='post' action='?action=verify-mail'>\n    <button type='submit'>Générer un nouveau token de vérification</button>\n   </form>";
        $info = "";
        $info .= $user->nom ? "<p>Nom : {$user->nom}</p>" : "";
        $info .= $user->prenom ? "<p>Prénom : {$user->prenom}</p>" : "";
        return <<<FIN
        <div class="user">
            <p>{$user->email}</p>
            {$verif}
            <div>
                {$info}
            </div>

            <a href="?action=profil-info">Modifier les informations de profil</a>

        </div>
        FIN;
    }

}