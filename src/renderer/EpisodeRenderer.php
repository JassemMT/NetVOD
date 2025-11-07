<?php
declare(strict_types=1);
namespace netvod\renderer;

use netvod\classes\Episode;
use netvod\renderer\Renderer;
class EpisodeRenderer implements Renderer {

    protected Episode $episode;

    public function __construct(Episode $episode) {
        $this->episode = $episode;
    }
    
    public function render(): string {
        $episode = $this->episode;
        return <<<FIN
        <div class="episode">
            <h3>{$episode->titre}</h3>
            <p>numéro: {$episode->numero}</p>
            <p>Durée: {$episode->duree} secondes</p>
            <p>{$episode->description}</p>
            <a href="?action=display-serie&id={$episode->id}"><img src="{$episode->image}" alt="{$episode->title}"/></a>
        </div>
        FIN;
    }

}