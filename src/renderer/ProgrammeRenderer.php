<?php
declare(strict_types=1);
namespace netvod\renderer;

use netvod\classes\Programme;

abstract class ProgrammeRenderer implements Renderer {

    protected Programme $programme;

    public function __construct(Programme $programme) {
        $this->programme = $programme;
    }

    public function render(array $params = []): string {
        $programme = $this->programme;
        return <<<FIN
        <div class="programme">
                <h2>{$programme->getTitre()}</h2>
                <p>{$programme->getSynopsis()}</p>
                <img src="{$programme->getImage()}" alt="{$programme->getTitre()}"/>
        </div>
        FIN;
    }

}