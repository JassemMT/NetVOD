<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;

use netvod\classes\Commentaire;

class NotationAction implements Action {

    public static function execute(): string { // action distincte ??? notation sur une page à part ??? 

        // on récupère l'id de la série en session ou via le SerieRepository ??? quelle série en session ?
        // $Sid = (int)$_SESSION['idSerie'];     
        $Srender = SerieRenderer();
        $Srep = SerieRepository::GetInstance(); 

        if (isset($_GET['id'])) $id = $_GET['id'];
        else throw new MissingArgumentException('id');

        $Serie = $Srep->findById($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $Srender->render($Srep->findAll());

            return <<<FIN
                <h1>Noter</h1>
                    <form action="?action=noter&id={$id}" method="post" class="note-form">
                        <label for="titre">Titre de  la série</label>
                        <input type="text" id="titre" placeholder="Nom Série" name="titre" required>

                        <label for="commentaire">commentaire</label>
                        <input type="text" id="commentaire" placeholder="commentaire" name="commentaire" required>

                        <label for="note">Note pour la série</label>
                        <input type="int" min="1" max="5" step="1" id="note" placeholder="Note" name="note" required>

                        <button type="submit" class="btn-primary">Envoyer</button>
                    </form>
            FIN;
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['titre'])) { // check si le titre est bien défini dans la BD
                if (isset($_POST['note'])) {
                    if (is_numeric($_POST['note'])) {
                        if (isset($_POST['commentaire'])) {
                            $titre = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
                            $note = $_POST['note'];
                            $commentaire = filter_var($_POST['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS);
                            $idS = $Srep->findByTitle($titre);

                            $com = new Commentaire($idS,$note,$commentaire);
                            $comRep = CommentaireRepository::GetInstance();
                            $comRep->upload($com);

                            return "";
                        } else throw new MissingArgumentException("commentaire");
                    } else throw new InvalidArgumentException("note");
                } else throw new MissingArgumentException("titre");
            } else throw new MissingArgumentException("titre");
        } else throw new BadRequestMethodException();
    }   
    
}