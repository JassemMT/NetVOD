<?php
declare(strict_types=1);

namespace netvod\action;

use NetVOD\Classes\User;
use netvod\exception\AuthException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\exception\ActionUnauthorizedException;
use netvod\auth\AuthnProvider;
use netvod\repository\UserRepository;


class DisplayListeProgrammeAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
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
        }else throw new AuthException("il faut être connecté pour voir une liste de programmes");

    }
}