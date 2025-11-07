<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\repository\SerieRepository;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\exception\BadRequestMethodException;

class DisplayCatalogueAction implements Action {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $rep = SerieRepository::GetInstance();
            $catalogue = $rep->findAll(); //Objet de type ListeProgramme
            var_dump($catalogue);

            $listeprogrammeRenderer = new ListeProgrammeRenderer($catalogue);
            return $listeprogrammeRenderer->render();
        }else throw new BadRequestMethodException();
    }
}