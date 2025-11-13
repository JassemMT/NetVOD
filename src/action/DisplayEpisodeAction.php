<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\auth\AuthzProvider;
use netvod\exception\AuthnException;
use netvod\exception\MissingArgumentException;
use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\form\NotationFormRenderer;
use netvod\repository\EpisodeRepository;
use netvod\repository\UserRepository;
use netvod\exception\AuthzException;

class DisplayEpisodeAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
            if (AuthzProvider::isVerified()) {
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
            } else throw new AuthzException("Il faut avoir vérifié son compte pour voir un épisode");
        } else throw new AuthnException("il faut être connecté pour voir un épisode");
    }
}