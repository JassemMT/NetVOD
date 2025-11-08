<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\renderer\form\NotationFormRenderer;
use netvod\repository\SerieRepository;
use netvod\repository\CommentaireRepository;
use netvod\classes\Commentaire;

class NotationAction implements Action {

    public function execute(): string { // action distincte ??? notation sur une page à part ??? 

        // on récupère l'id de la série en session ou via le SerieRepository ??? quelle série en session ?
        // $Sid = (int)$_SESSION['idSerie'];

        if (isset($_GET['id'])) $id = $_GET['id'];
        else throw new MissingArgumentException('id');

        $Serie = SerieRepository::findById($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return (new NotationFormRenderer($id))->render();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['titre'])) { // TODO: check si le titre est bien défini dans la BD
                if (isset($_POST['note'])) {
                    if (is_numeric($_POST['note'])) {
                        if (isset($_POST['commentaire'])) {
                            $titre = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
                            $note = $_POST['note'];
                            $commentaire = filter_var($_POST['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS);

                            $com = new Commentaire($id,AuthnProvider::getSignedInUser(), $note,$commentaire);
                            CommentaireRepository::upload($com); // TODO: créer le repository Commentaire

                            return "";
                        } else throw new MissingArgumentException("commentaire");
                    } else throw new InvalidArgumentException("note");
                } else throw new MissingArgumentException("titre");
            } else throw new MissingArgumentException("titre");
        } else throw new BadRequestMethodException();
    }   
    
}