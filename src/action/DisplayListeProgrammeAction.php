<?php
declare(strict_types=1);

namespace netvod\action;
use netvod\action\Action;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\ListeProgrammeRenderer;

class DisplayListeProgrammeAction implements Action {
    public static function execute(): string {
        print("Affichage de la liste demandée <br>");


        if ($_SERVER['REQUEST_METHOD']==='GET') {
            $idListe = $_GET['idListe'] ?? -1;
            if ($idListe === -1){
                echo "L'id de la liste n'est pas renseigné / pas en session / pas en query string";
            }
            else{
                $rep = ListeProgrammeRepository::GetInstance();
                $pl = $rep->getProgrammes($idListe);
                var_dump($pl);
                
                $lr = new ListeProgrammeRenderer();
                $lr->render($pl);
            }
        }
        else throw new BadRequestMethodException();

    }
}