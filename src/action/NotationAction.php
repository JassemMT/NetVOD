<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\BadRequestMethodArgumentException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;

use netvod\classes\Commentaire;

class NotationAction implements Action {

    public static function execute(){

        // on récupère l'id de la série en session ou via le SerieRepository
        // $Sid = (int)$_SESSION['idSerie'];     
        $Srender = SerieRenderer();
        $Srep = SerieRepository::GetInstance(); 
        // on récupère la série avec l'id correspondant 
        $Serie = $Srep->findById($_GET['idSerie']) ?? -1;
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($Serie === -1){
                $Srender->render($Srep->findAll());

                echo <<<FIN
                    <h1>Noter</h1>
                        <form action="?action=noter" method="post" class="note-form">
                            <label for="titre">Titre de  la série</label>
                            <input type="text" id="titre" placeholder="Nom Série" name="titre" required>

                            <label for="commentaire">commentaire</label>
                            <input type="text" id="commentaire" placeholder="commentaire" name="commentaire" required>

                            <label for="note">Note pour la série</label>
                            <input type="int" min="1" max="5" step="1" id="note" placeholder="Note" name="note" required>

                            <button type="submit" class="btn-primary">Envoyer</button>
                        </form>
                FIN;
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['titre'])) {
                if (filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS) !== null) {
                    $titre = $_POST['titre'];
                    $note = filter_var($_POST['note'],FILTER_VALIDATE_INT);
                    $avis = filter_var($_POST['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $idS = $Srep->findByTitle($titre);

                    $com = new Commentaire($idS,$note,$avis);
                    $comRep = CommentaireRepository::GetInstance();
                    $comRep->upload($com);

                    return "";
                } else throw new InvalidArgumentException("titre");
            } else throw new MissingArgumentException("titre");
        } else throw new BadRequestMethodException();
    }   
    
}