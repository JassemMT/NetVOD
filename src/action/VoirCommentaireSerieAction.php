<?php
declare(strict_types= 1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\renderer\CommentaireRenderer;

class VoirCommentaireSerieAction implements Action {
    public function execute(): string {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            if (isset($_GET["serie"])) {
                $idSerie = (int)$_GET["serie"];
                $commentaireRenderer = new CommentaireRenderer($idSerie);
                return $commentaireRenderer->render();
            } else throw new MissingArgumentException("serie");
        } else throw new BadRequestMethodException();
    }
}