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
        <header class="episode-header">
            <h1 id="episode-title">{$episode->titre}</h1>
            <p class="episode-meta">Épisode {$episode->numero} — Durée : {$episode->duree} secondes</p>
        </header>

        <section class="episode-player" aria-label="Lecteur vidéo">
            <video id="player" class="player" controls preload="metadata" playsinline controlsList="nodownload" poster="{$episode->source}" aria-label="Vidéo — {$episode->titre}" >
            <source src="{$episode->source}" type="video/mp4">
            Votre navigateur ne supporte pas la lecture de vidéo.
            </video>
        </section>
        FIN;
    }

    public function renderShort(): string {
        $episode = $this->episode;
        
        return <<<FIN
        <article class="episode-card" role="article">
            <a href="?action=display-episode&id={$episode->id}" class="episode-link" aria-label="Épisode {$episode->numero} — {$episode->titre}">
                
                <!-- Image -->
                <div class="episode-image-wrapper">
                    <img class="episode-image" src="{$episode->image}" alt="<?= htmlspecialchars($episode->titre) ?>" />
                </div>

                <!-- Info band -->
                <div class="episode-band">
                    <div class="episode-meta">
                        <h3 class="episode-title">Épisode {$episode->numero}</h3>
                        <p class="episode-subtitle">{$episode->titre}</p>
                        <p class="episode-duration">
                            <time>{$episode->duree} secondes</time>
                        </p>
                    </div>
                    <button class="btn btn-small" role="button">Regarder</button>
                </div>

            </a>
        </article>
        FIN;
    }

}