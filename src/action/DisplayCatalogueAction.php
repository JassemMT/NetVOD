<?php
declare(strict_types=1);
namespace netvod\action;


use netvod\action\Action;

class DisplayCatalogueAction implements Action {
    function execute(): string {
        print("Affichage du catalogue <br>");

        $rep = RepositoryDeefy::GetInstance();
        $pl = $rep->afficherCatalogue();
        var_dump($pl); 
        }
}