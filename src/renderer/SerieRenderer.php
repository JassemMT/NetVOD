<?php
declare(strict_types= 1);
namespace netvod\renderer;

use netvod\classes\Episode;
use netvod\renderer\EpisodeRenderer;

class SerieRenderer implements Renderer {
    public function render(array $params = []): string {
        $serie = $params["serie"];
        $episodes = "";
        foreach ($serie->episodes as $episode) {
            $episodes .= EpisodeRenderer::render(["episode" => $episode]);
        }
        return <<<FIN
        <div class="serie">
            <h2>{$serie->title}</h2>
            <p>{$serie->description}</p>
            <p>{$serie->annee}</p>
            <img src="{$serie->image}" alt="{$serie->title}"/>
            <div class="episodes">
                $episodes
            </div>
        FIN;
    }
}