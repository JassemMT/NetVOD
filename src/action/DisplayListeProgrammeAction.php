<?php
declare(strict_types=1);

namespace netvod\action;

use netvod\auth\AuthzProvider;
use netvod\exception\AuthnException;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\auth\AuthnProvider;
use netvod\repository\UserRepository;


class DisplayListeProgrammeAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
            if (AuthzProvider::isVerified()) {
                if ($_SERVER['REQUEST_METHOD']==='GET') {
                    
                    $programmes = UserRepository::getUserLists(AuthnProvider::getSignedInUser());
                    $html = "";
                    
                    $favRenderer = new ListeProgrammeRenderer($programmes['favoris']);
                    $enCoursRenderer = new ListeProgrammeRenderer($programmes['en_cours']);
    
                    $html .= "<h2>Favoris</h2>";
                    $html .= $favRenderer->render();
                    $html .= "<br/><br/><hr/><br/><br/>";
                    $html .= "<h2>En cours</h2>";
                    $html .= $enCoursRenderer->render();
    
                    return $html;
                
                }else throw new BadRequestMethodException();
            } else throw new AuthnException("Il faut avoir vérifié son compte pour voir ses listes de programmes");
        }else throw new AuthnException("il faut être connecté pour voir une liste de programmes");

    }
}