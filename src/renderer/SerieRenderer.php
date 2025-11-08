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
            $renderer = new EpisodeRenderer($episode);
            $episodes .= $renderer->renderShort();
        }

        return <<<FIN
        <div class="serie">
            <h2>{$serie->title}</h2>
            <p>{$serie->description}</p>
            <p>{$serie->annee}</p>
            <img src="{$serie->image}" alt="{$serie->title}"/>
        </div>
        <hr/>
        <div class="episodes">
            $episodes
        </div>
        FIN;
    }

    public function renderShort(): string {
        $serie = $this->serie;
        return <<<FIN
        <div class="serie-short">
            <a href="?action=display-serie&id={$serie->id}", style="text-decoration: none; color: inherit;">
                <h2>{$serie->title}</h2>
                <p>{$serie->description}</p>
                <p>{$serie->annee}</p>
                <img src="{$serie->image}" alt="{$serie->title}"/>
            </a>
        FIN;
    }
}