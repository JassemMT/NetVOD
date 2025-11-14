<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\AuthnException;
use netvod\repository\UserRepository;
use netvod\renderer\ListeProgrammeRenderer;

class DefaultAction implements Action {
    public function execute() : string {
        if (AuthnProvider::isLoggedIn()) {
            $programmes = UserRepository::getUserLists(AuthnProvider::getSignedInUser());
            $favRenderer = new ListeProgrammeRenderer($programmes['favoris']);
            $fav = $favRenderer->render();

            return <<<FIN
            <h1>Bienvenue sur NetVOD</h1>
            <p>Votre plateforme de streaming préférée !</p>
            <h2>Vos favoris en un coup d'œil :</h2>
            {$fav}
            FIN;

        } else throw new AuthnException('Vous devez être connecté pour accéder à cette page.');
    }
}