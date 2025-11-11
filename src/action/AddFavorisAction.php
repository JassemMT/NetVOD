<?php

declare(strict_types=1);

namespace netvod\action;

use Exception;
use netvod\repository\SerieRepository;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\exception\ActionUnauthorizedException;
use netvod\auth\AuthnProvider;
use netvod\repository\UserRepository;
use netvod\exception\MissingArgumentException;


class AddFavorisAction implements Action
{


    //on va faire appel à UserRepository::addSerieToList(int $id_user, int $id_serie, string $listName)
    // $_SESSION['user_id'] pour l'id_user
    // $_POST['id'] pour l'id_serie
    // 'favoris' pour le listName
    //vérifier que l'utilisateur est connecté

    public function execute(): string
    {
        if (AuthnProvider::isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['id'])) {
                    $id_user = $_SESSION['user'];
                    $id_serie = (int)$_POST['id'];
                    $listName = 'favoris';

                    $result = UserRepository::addSerieToList($id_user, $id_serie, $listName);
                    if ($result) {

                        try {
                                header('Location: ?action=display-serie&id='.$id_serie);
                                exit;
                            } catch (Exception $e) {
                                return "Série ajoutée aux favoris avec succès. Cependant, une erreur est survenue lors de la redirection.";
                            }

                    } else {
                        throw new \PDOException("Erreur lors de l'ajout de la série aux favoris.");
                    }
                } else {
                    throw new MissingArgumentException("id");
                }
            } else {
                throw new BadRequestMethodException();
            }
        } else {
            throw new ActionUnauthorizedException("il faut être connecté pour ajouter une série aux favoris");
        }

    }
}