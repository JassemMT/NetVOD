<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\SerieRenderer;
use netvod\exception\BadRequestMethodException;

class DisplaySerieAction implements Action {
    public function execute(): string {
        print("Affichage de la série : <br>");

        if ($_SERVER['REQUEST_METHOD']==='GET') {
            $idSerie = $_GET['idSerie'] ?? -1;
            if ($idSerie === -1){
                echo "L'id de la série n'est pas renseigné / pas en session / pas en query string";
            }
            else{
                $rep = SerieRepository::GetInstance();
                $pl = $rep->findById($idSerie);
                var_dump($pl);
                
                $lr = new SerieRenderer();
                $lr->render((array)$pl);
            }
        }
        else throw new BadRequestMethodException();


        


        // pour utiliser et appeler le SerieRenderer() il faut récupérer d'une manière ou d'une autre 
        // une liste de série et plus spécifiquement la série ou la liste de série voulu par l'utilisateur 
        // choix parmis les listes enregistrés de l'utilisateur 
        // ou sinon parmis toutes les séries possibles 
        // $red = new SerieRenderer();
        // $red->render();


    }
}