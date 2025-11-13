<?php

declare(strict_types=1);

namespace netvod\action;


use netvod\auth\AuthzProvider;
use netvod\exception\AuthnException;
use netvod\exception\AuthzException;
use netvod\notification\Notification;
use netvod\exception\BadRequestMethodException;
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
            if (AuthzProvider::isVerified()) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['id'])) {
                        $id_user = $_SESSION['user'];
                        $id_serie = (int)$_POST['id'];
                        $listName = 'favoris';
    
                        $result = UserRepository::addSerieToList($id_user, $id_serie, $listName);
                        if ($result) {
                            Notification::save("Série ajoutée aux favoris.", "Succès", Notification::TYPE_SUCCESS);
                            header('Location: ?action=display-serie&id='.$id_serie);
                            return "";
                            
                        } else throw new \PDOException("Erreur lors de l'ajout de la série aux favoris.");
                    } else throw new MissingArgumentException("id");
                } else throw new BadRequestMethodException();
            } else throw new AuthzException("Vous devez vérifier votre compte pour ajouter une série aux favoris.");
        } else throw new AuthnException("il faut être connecté pour ajouter une série aux favoris");
    }
}