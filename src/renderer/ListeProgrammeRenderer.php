<?php
declare(strict_types= 1);
namespace netvod\renderer;

use NetVOD\Classes\ListeProgramme;
use netvod\Classes\Serie;

class ListeProgrammeRenderer implements Renderer {

    private ListeProgramme $lstprogramme;

    public function __construct(ListeProgramme $lstprogramme) {
        $this->lstprogramme = $lstprogramme;
    }

    public function render(): string {
        $html = '';
        foreach ($this->lstprogramme->getProgrammes() as $programme) {
            $html .= (new SerieRenderer($programme))->renderShort(); // on part du principe que la liste de programme ne contient que des séries
        }
        return <<<FIN
        <section class="section-catalogue">
                <!-- GRID DE CARTES -->
                <div class="grid" role="region" aria-label="Liste des séries">
                    {$html}
                </div>
        </section>
        FIN;

    }
}