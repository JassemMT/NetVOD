<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\MissingArgumentException;
use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\form\NotationFormRenderer;
use netvod\repository\EpisodeRepository;
use netvod\exception\ActionUnauthorizedException;

class DisplayEpisodeAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (isset($_GET['id'])) {
                    $id = (int)$_GET['id'];
                    $episode = EpisodeRepository::findById($id);
                    if ($episode === null) {
                        throw new \PDOException("Épisode introuvable dans la base de données.");
                    }
                    $id_serie = EpisodeRepository::findSerieByID($id);
            
                    $html = "";
                    $renderer = new EpisodeRenderer($episode);
                    $html .= $renderer->render();
                    $renderer = new NotationFormRenderer($id_serie);
                    $html .= $renderer->render();
                    return $html;
                } else throw new MissingArgumentException("id");
            } else throw new BadRequestMethodException();
        } else throw new ActionUnauthorizedException("il faut être connecté pour voir un épisode");
    }
}