<?php
declare(strict_types=1);
namespace netvod\renderer;

abstract class ProgrammeRenderer implements Renderer {
    public static function render(array $params = []): string {
        $programme = $params["programme"];
        return <<<FIN
        <div class="programme">
                <h2>{$programme->getTitre()}</h2>
                <p>{$programme->getSynopsis()}</p>
                <img src="{$programme->getImage()}" alt="{$programme->getTitre()}"/>
        </div>
        FIN;
    }

}