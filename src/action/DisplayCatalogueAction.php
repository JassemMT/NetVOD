<?php
declare(strict_types=1);
namespace netvod\action;


use netvod\action\Action;

class DisplayCatalogueAction extends Action {
    function execute() {
        print("Affichage du catalogue <br>");

        $rep = RepositoryDeefy::GetInstance();
        $pl = $rep->afficherCatalogue();
        var_dump($pl); 
        }
}