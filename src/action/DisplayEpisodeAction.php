<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\AuthException;
use netvod\exception\MissingArgumentException;
use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\form\NotationFormRenderer;
use netvod\repository\EpisodeRepository;
use netvod\repository\UserRepository;

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

                    $id_user = AuthnProvider::getSignedInUser();
                    if (EpisodeRepository::estPremierEpisode($episode->id)) {
                        UserRepository::addSerieToList($id_user, $id_serie, 'en_cours');
                    }
                    elseif (EpisodeRepository::estDernierEpisode($episode->id)) {
                        UserRepository::removeSerieFromList($id_user, $id_serie, 'en_cours');
                    }

                    $html = "";
                    $renderer = new EpisodeRenderer($episode);
                    $html .= $renderer->render();

                    $renderer = new NotationFormRenderer($id_serie);
                    $html .= $renderer->render();
                    return $html;
                } else throw new MissingArgumentException("id");
            } else throw new BadRequestMethodException();
        } else throw new AuthException("il faut être connecté pour voir un épisode");
    }
}