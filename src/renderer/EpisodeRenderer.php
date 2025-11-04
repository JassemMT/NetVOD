<?php
declare(strict_types=1);
namespace netvod\renderer;

class EpisodeRenderer implements Renderer {
    public static function render(array $params = []): string {
        $episode = $params["episode"];
        return <<<FIN
        <div class="episode">
            <h3>{$episode->titre}</h3>
            <p>numéro: {$episode->numero}</p>
            <p>Durée: {$episode->duree} secondes</p>
            <p>{$episode->description}</p>
            <p>Season: {$episode->season}, Episode: {$episode->number}</p>
            <a href="{$episode->source}"><img src="{$episode->image}" alt="{$episode->title}"/></a>
        </div>
        FIN;
    }

}