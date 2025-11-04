<?php
declare(strict_types=1);

namespace NetVOD\action;
use NetVOD\action\Action;

class DisplayListeProgrammeAction extends Action {
    function execute() {
        print("Affichage de la liste demandée <br>");


        $idListe = $_GET['idListe'] ?? -1;
        if ($idListe === -1){
            echo "L'id de la liste n'est pas renseigné";
        }
        else{
            $rep = RepositoryDeefy::GetInstance();
            $pl = $rep->afficheListe($idListe);
            var_dump($pl);    
            
        }
    }
}