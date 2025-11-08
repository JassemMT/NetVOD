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
        <article class="card" role="article" tabindex="0" aria-label="{$serie->titre} — Série de {$serie->annee}">
            <img class="card-image" src="{$serie->image}" alt="{$serie->titre}" />
            <div class="card-band">
                <div class="card-meta">
                    <h3 class="card-title">{$serie->titre}</h3>
                    <p class="card-genre">{$serie->annee}</p>
                </div>
                <a href="?action=display-serie&id={$serie->id}" class="btn" role="button">Regarder</a>
            </div>
        </article>
        FIN;
    }
}


