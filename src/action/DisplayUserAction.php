<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\MissingArgumentException;
use netvod\repository\UserRepository;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\UserRenderer;

class DisplayUserAction implements Action {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $renderer = new UserRenderer(AuthnProvider::getSignedInUser());
                return $renderer->render();
        } else throw new BadRequestMethodException();
    }
}
