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

        foreach ($this->lstprogramme->getProgrammes() as $programme) {
            $html = '<div class="serie">'.
                    '<h2>'.$programme->titre.'</h2>'.
                    '<p>'.$programme->description.'</p>'.
                    '<img src="'.$programme->image.'" alt="'.$programme->titre.'"/>'.
                    '</div>';
        }
        return <<<FIN
        <h1>Catalogue des séries</h1>
        <div class="liste-programme"> $html </div>
        FIN;

    }
}