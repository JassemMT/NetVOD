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
            $html .= <<<FIN
            <div class="serie">
                <a href="?action=display-serie&id={$programme->id}" style="text-decoration: none; color: black;">
                    <h2>{$programme->titre}</h2>
                    <p>{$programme->description}</p>
                    <img src="{$programme->image}" alt="{$programme->titre}"/>
                </a>
            </div>
            FIN;
        }
        return <<<FIN
        <h1>Catalogue des séries</h1>
        <div class="liste-programme"> $html </div>
        FIN;

    }
}