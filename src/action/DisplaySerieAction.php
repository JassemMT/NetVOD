<?php
declare(strict_types=1);
namespace NetVOD\action;

use NetVOD\renderer\SerieRenderer;

class DisplaySerieAction extends Action {
    public function execute() {
        print("Affichage de la série : <br>");

        $rep = RepositoryDeefy::GetInstance();
        $pl = $rep->afficherSerie();
        var_dump($pl);

        // $red = new SerieRenderer();
        // $red->render();


    }
}