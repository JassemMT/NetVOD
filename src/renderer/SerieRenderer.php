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
        <!-- HERO SECTION -->
        <section class="hero-serie" role="region" aria-label="Détails de la série">
            <div class="hero-image">
                <img src="{$serie->image}" alt="{$serie->titre}" />
            </div>
            
            <div class="hero-content">
                <div class="hero-meta">
                    <h1 class="hero-title">{$serie->titre}</h1>
                    <p class="hero-year">{$serie->annee}</p>
                </div>
    
                <div class="hero-actions">
                    <form method="POST" action="?action=add-favoris">
                        <input type="hidden" name="serie_id" value="{$serie->id}">
                        <button class="btn btn-secondary" aria-label="Ajouter {$serie->titre} à mes favoris">
                            Ajouter aux favoris
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="episodes-section" role="region" aria-label="Épisodes de la série">
            <h2>Épisodes</h2>
            <div class="episodes-grid">
                {$episodes}
            </div>
        </section>
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


