<?php
declare(strict_types=1);
namespace NetVOD\action;


use NetVOD\action\Action;

class DisplayCatalogueAction extends Action {
    function execute() {
        print("Affichage du catalogue <br>");

        $rep = RepositoryDeefy::GetInstance();
        $pl = $rep->afficherCatalogue();
        var_dump($pl); 
        }
}