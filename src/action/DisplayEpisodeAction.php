<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;

class DisplayEpisodeAction implements Action {
<<<<<<< HEAD
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
=======
    public function execute(): string {
        
        $rep = EpisodeRepository::GetInstance(); 

        $episode = $rep->afficher();
        var_dump($episode);

        return EpisodeRenderer::render(["episode" => $episode]);
>>>>>>> 069b4692620ef18cbb063bd81a989b9ab4fdefb6
    }
}