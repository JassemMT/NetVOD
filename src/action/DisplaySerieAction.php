<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\MissingArgumentException;
use netvod\renderer\SerieRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\repository\SerieRepository;

class DisplaySerieAction implements Action {
    public function execute(): string {

        if ($_SERVER['REQUEST_METHOD']==='GET') {
            if (isset($_GET['id'])) {
                $idSerie = $_GET['idSerie'];
                $rep = SerieRepository::GetInstance();
                $pl = $rep->findById($idSerie);
                var_dump($pl);
                
                $lr = new SerieRenderer();
                $lr->render((array)$pl);

            } else throw new MissingArgumentException('id');
        } else throw new BadRequestMethodException();


        


        // pour utiliser et appeler le SerieRenderer() il faut récupérer d'une manière ou d'une autre 
        // une liste de série et plus spécifiquement la série ou la liste de série voulu par l'utilisateur 
        // choix parmis les listes enregistrés de l'utilisateur 
        // ou sinon parmis toutes les séries possibles 
        // $red = new SerieRenderer();
        // $red->render();


    }
}