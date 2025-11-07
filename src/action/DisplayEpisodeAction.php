<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\form\NotationFormRenderer;
use netvod\repository\EpisodeRepository;

class DisplayEpisodeAction implements Action {
    public static function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $rep = EpisodeRepository::GetInstance();
    
            $episode = $rep->afficher();
            var_dump($episode);
    
            $html = "";
            $html .= EpisodeRenderer::render(["episode" => $episode]);
            //$notation = new NotationAction();
            //$html .= $notation->execute();
            $html .= NotationFormRenderer::render(['id' => $episode->getId()]);
            return $html;
        } else throw new BadRequestMethodException();
    }
}