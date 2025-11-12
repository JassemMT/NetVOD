<?php
declare(strict_types= 1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\token\TokenManager;

class VerifierMailAction implements Action {    public function execute(): string {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            if (isset($_GET["token"])) {
                if (TokenManager::useToken($_GET["token"])) {
                    return "<h2>Email vérifié avec succès</h2><p>Votre adresse email a été confirmée.</p>";
                } else {
                    return "<h2>Erreur de vérification</h2><p>Le token est invalide ou a expiré.</p>";
                }
            } else {
                return "<h2>Erreur</h2><p>Aucun token de vérification fourni.</p>";
            }
        } else {
            throw new BadRequestMethodException();
        }
    }
}
