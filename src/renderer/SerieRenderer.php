<?php
declare(strict_types= 1);
namespace netvod\renderer;

use netvod\classes\Episode;
use netvod\renderer\EpisodeRenderer;
use netvod\classes\Serie;

class SerieRenderer extends ProgrammeRenderer implements Renderer {

    private Serie $serie;

    public function __construct(Serie $serie) {
        $this->serie = $serie;
    }

    public function render(): string {
        $serie = $this->serie;
        if (!$serie instanceof Serie) throw new \Exception("Le paramètre 'serie' doit être une instance de Serie.");
        $episodes = "";
        foreach ($serie->episodes as $episode) {
            $episodes .= EpisodeRenderer::render(["episode" => $episode]);
        }
        return <<<FIN
        <div class="serie">
            <a href="?action=display-serie&id={$serie->id}">
                <h2>{$serie->title}</h2>
                <p>{$serie->description}</p>
                <p>{$serie->annee}</p>
                <img src="{$serie->image}" alt="{$serie->title}"/>
            </a>
            <div class="episodes">
                $episodes
            </div>
        FIN;
    }
}