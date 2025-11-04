<?php

namespace netvod\action;

class DefaultAction implements Action{



    public function execute(): string{
        if(isset($_SESSION['user'])){
            return '<h1>Bienvenue ' . substr($_SESSION['user']['email'], 0, strpos($_SESSION['user']['email'], '@')) . ' !</h1>';
        } else {
            return '<h1>Bienvenue sur Deefy !</h1>
            <p>Veuillez vous connecter ou créer un compte pour accéder à toutes les fonctionnalités.</p>';
        }
    }

}
