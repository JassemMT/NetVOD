<?php
declare(strict_types=1);
namespace netvod\renderer;

use netvod\auth\AuthzProvider;
use netvod\classes\User;
use netvod\token\TokenManager;

class UserRenderer implements Renderer {

    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function render(): string {
        $user = $this->user;
        $verif = AuthzProvider::isVerified()? "Vérifié" : "Non vérifié";
        return <<<FIN
        <div class="user">
            <p>{$user->email}</p>
            <p>{$verif}</p>
            <form method="post" action="?action=verify-mail">
                <button type="submit">Générer un nouveau token de vérification</button>
            </form>
        </div>
        FIN;
    }

}