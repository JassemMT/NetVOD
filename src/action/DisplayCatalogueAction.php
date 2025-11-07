<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\repository\SerieRepository;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\exception\ActionUnauthorizedException;
use netvod\auth\AuthnProvider;
class DisplayCatalogueAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $catalogue = SerieRepository::findAll(); //Objet de type ListeProgramme

                $listeprogrammeRenderer = new ListeProgrammeRenderer($catalogue);
                return $listeprogrammeRenderer->render();
            }else throw new BadRequestMethodException();
        }else throw new ActionUnauthorizedException("il faut être connecté pour voir le catalogue");
    }
}