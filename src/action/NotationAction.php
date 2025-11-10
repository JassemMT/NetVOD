<?php
declare(strict_types=1);

namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;
use netvod\renderer\form\NotationFormRenderer;
use netvod\repository\SerieRepository;

class NotationAction implements Action {

    public function execute(): string {

        if (!isset($_GET['id'])) {
            throw new MissingArgumentException('id');
        }

        $idSerie = (int)$_GET['id'];
        $user = AuthnProvider::getSignedInUser(); // Doit retourner un objet ou un id_user
       
        // GET : affichage du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return (new NotationFormRenderer($idSerie))->render();
        }

        // POST : traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérification des champs
            if (!isset($_POST['note'])) {
                throw new MissingArgumentException("note");
            }
            if (!isset($_POST['commentaire'])) {
                throw new MissingArgumentException("commentaire");
            }

            $note = (int)$_POST['note'];
            $commentaire = trim(filter_var($_POST['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS));

            if ($note < 0 || $note > 5) {
                throw new InvalidArgumentException("La note doit être entre 0 et 5.");
            }

            // Vérifie si l’utilisateur a déjà noté la série
            if (SerieRepository::hasUserCommented($user, $idSerie)) {
                SerieRepository::updateComment($user, $idSerie, $note, $commentaire);
            } else {
                SerieRepository::addComment($user, $idSerie, $note, $commentaire);
            }

            // Optionnel : feedback utilisateur
            $moyenne = (new SerieRepository())->getAverageRating($idSerie);
            return "<p>Votre avis a été enregistré ! Note moyenne actuelle : " . number_format($moyenne, 2) . "/5</p>";
        }

        throw new BadRequestMethodException();
    }
}
