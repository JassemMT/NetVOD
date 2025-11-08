<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\MissingArgumentException;
use netvod\renderer\SerieRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\repository\SerieRepository;
use netvod\exception\InvalidArgumentException;
use netvod\exception\ActionUnauthorizedException;
use netvod\auth\AuthnProvider;

class DisplaySerieAction implements Action {
    public function execute(): string {

        if (AuthnProvider::isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD']==='GET') {
            if (isset($_GET['id'])) {
                    if (!empty($_GET['id'])) {
                        $idSerie = $_GET['id'];
                        $listeP = SerieRepository::findById($idSerie);
                        
                        $renderer = new SerieRenderer($listeP);
                        return $renderer->render();
                    } else throw new InvalidArgumentException('id');
                } else throw new MissingArgumentException('id');
            } else throw new BadRequestMethodException();
        } else throw new ActionUnauthorizedException("il faut être connecté pour voir une série");

        // pour utiliser et appeler le SerieRenderer() il faut récupérer d'une manière ou d'une autre 
        // une liste de série et plus spécifiquement la série ou la liste de série voulu par l'utilisateur 
        // choix parmis les listes enregistrés de l'utilisateur 
        // ou sinon parmis toutes les séries possibles 
        // $red = new SerieRenderer();
        // $red->render();

    }
}