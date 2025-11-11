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
            
                    $html = "";
                    $renderer = new EpisodeRenderer($episode);
                    $html .= $renderer->render();
                    //var_dump($episode->id);
                    // test si l'id de l'épisode correspond à l'id du premier épisode d'une des séries 
                    if ($episode->id == 1 || $episode->id == 6 || $episode->id == 11 || $episode->id == 14 || $episode->id == 17 ||$episode->id == 20 ){
                        $id_user = $_SESSION['user'];
                        $listName = 'en_cours';
                        $r = UserRepository::addSerieToList($id_user, $id_serie, $listName);
                        //var_dump($r);
                    }
                    // test si l'id de l'épisode correspond à l'id du dernier épisode d'une des séries 
                    elseif ($episode->id == 5 || $episode->id == 10 || $episode->id == 13 || $episode->id == 16 || $episode->id == 19 ||$episode->id == 21 ) {
                        $id_user = $_SESSION['user'];
                        $listName = 'en_cours';
                        $r = UserRepository::removeSerieFromList($id_user, $id_serie, $listName);
                        //var_dump($r);   
                    }


                    $renderer = new NotationFormRenderer($id_serie);
                    $html .= $renderer->render();
                    return $html;
                } else throw new MissingArgumentException("id");
            } else throw new BadRequestMethodException();
        } else throw new AuthException("il faut être connecté pour voir un épisode");
    }
}