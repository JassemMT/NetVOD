<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\SerieRenderer;

class DisplaySerieAction implements Action {
    public static function execute(): string {
        print("Affichage de la série : <br>");

        $rep = RepositoryDeefy::GetInstance();
        $pl = $rep->afficherSerie();
        var_dump($pl);



        // pour utiliser et appeler le SerieRenderer() il faut récupérer d'une manière ou d'une autre 
        // une liste de série et plus spécifiquement la série ou la liste de série voulu par l'utilisateur 
        // choix parmis les listes enregistrés de l'utilisateur 
        // ou sinon parmis toutes les séries possibles 
        // $red = new SerieRenderer();
        // $red->render();


    }
}