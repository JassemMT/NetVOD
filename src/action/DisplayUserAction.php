<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\UserRenderer;

class DisplayUserAction implements Action {
    public static function execute(): string {
        print("Affichage du user : <br>");

        $rep = UserRepository::GetInstance();

        $user = $_GET['user'] ?? -1;
        if ($user === -1){
            echo " Pas d'utilisateur connecté";
        }
        else {return $user;}
        var_dump($user);

        return UserRenderer::render(["user" => $user]);
    }
}
