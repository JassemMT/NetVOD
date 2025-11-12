<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\AuthException;
use netvod\exception\MissingArgumentException;
use netvod\repository\UserRepository;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\UserRenderer;

class DisplayUserAction implements Action {
    public function execute(): string {
        if (AuthnProvider::isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $user = UserRepository::getUserById(AuthnProvider::getSignedInUser());
                    $renderer = new UserRenderer($user);
                    return $renderer->render();
            } else throw new BadRequestMethodException();
        }else throw new AuthException("il faut être connecté pour voir un utilisateur");
    }
}
