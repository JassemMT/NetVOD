<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\ListeProgrammeRenderer;

class DisplayCatalogueAction implements Action {
    public function execute(): string {
        $rep = CatalogueRepository::GetInstance();
        $catalogue = $rep->findAll();
        var_dump($catalogue);

        return ListeProgrammeRenderer::render(["lst" => $catalogue]);
        }
}