<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\notification\Notification;
use netvod\renderer\form\ProfilFormRenderer;
use netvod\auth\AuthnProvider;
use netvod\repository\UserRepository;

class ChangerProfilInfoAction implements Action {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $renderer = new ProfilFormRenderer();
            return $renderer->render();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['nom'])) {
                if (isset($_POST['prenom'])) {
                    $nom = trim($_POST['nom']);
                    $prenom = trim($_POST['prenom']);
                    $id_user = AuthnProvider::getSignedInUser();

                    UserRepository::updateProfilInfo($id_user, $nom, $prenom);

                    Notification::save("Les informations de profil ont été mises à jour.","Succès", Notification::TYPE_SUCCESS);
                    header("Location: ?action=display-user");
                    return "";
                } else throw new MissingArgumentException("prenom");
            } else throw new MissingArgumentException("nom");
        } else throw new BadRequestMethodException();
    }
}