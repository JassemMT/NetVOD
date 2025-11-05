<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;

class DisplayEpisodeAction implements Action {
    public static function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $rep = EpisodeRepository::GetInstance();
    
            $episode = $rep->afficher();
            var_dump($episode);
    
            $html = "";
            $html .= EpisodeRenderer::render(["episode" => $episode]);
            $notation = new NotationAction();
            $html .= $notation->execute();
        } else throw new BadRequestMethodException();
    }
}