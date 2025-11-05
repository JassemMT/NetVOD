<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\EpisodeRenderer;

class DisplayEpisodeAction implements Action {
    public static function execute(): string {
        
        $rep = EpisodeRepository::GetInstance(); 

        $episode = $rep->afficher();
        var_dump($episode);

        return EpisodeRenderer::render(["episode" => $episode]);
    }
}