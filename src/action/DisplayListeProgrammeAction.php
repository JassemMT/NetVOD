<?php
declare(strict_types=1);

namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\exception\ActionUnauthorizedException;

class DisplayListeProgrammeAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD']==='GET') {
                $idListe = $_GET['idListe'] ?? -1;
                if ($idListe === -1){
                    echo "L'id de la liste n'est pas renseigné / pas en session / pas en query string";
                }
                else{
                    $rep = ListeProgrammeRepository::GetInstance();
                    $programmes = $rep->getProgrammes($idListe); // lst de series dans les faits
                    var_dump($programmes);
                    return ListeProgrammeRenderer::render(['lst' => $programmes]);
                }
            }else throw new BadRequestMethodException();
        }else throw new ActionUnauthorizedException("il faut être connecté pour voir une liste de programmes");

    }
}