<?php
declare(strict_types= 1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\MissingArgumentException;

class AddSerieAction implements Action {
    public static function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // pas de GET pour cette action (clique droit avec js ou autre mais pas de formulaire)
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                if (is_numeric($id)) {
                    // Ajouter la série à la base de données (logique à implémenter)
                    return "";
                } else throw new InvalidArgumentException("Données invalides pour l'ajout de la série.");
            } else throw new MissingArgumentException("Tous les champs sont requis pour ajouter une série.");
        } else throw new BadRequestMethodException();
    }
}