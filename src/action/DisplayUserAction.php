<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\MissingArgumentException;
use netvod\repository\UserRepository;
use netvod\exception\BadRequestMethodException;
use netvod\renderer\UserRenderer;

class DisplayUserAction implements Action {
    public static function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_GET['user'])) {
                $user = $_GET['user'];
                var_dump($user);
                $rep = UserRepository::GetInstance();
                return UserRenderer::render(["user" => $user]);
            } else throw new MissingArgumentException('user');
        } else throw new BadRequestMethodException();
    }
}
