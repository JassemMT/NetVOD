<?php
declare(strict_types=1);
namespace netvod\action;
use netvod\renderer\SerieRenderer;

use netvod\action\Action;

class DisplayCatalogueAction implements Action {
    public static function execute() {
        print("Affichage du catalogue <br>");

        $rep = SerieRepository::GetInstance();
        $pl = $rep->findAll();
        var_dump($pl); 

        // Instanciation d'un serie renderer afin d'afficher toutes les séries du catalogue de manière structuré et complète
        $sr = new SerieRenderer();
        $sr->render($pl);

        }
}