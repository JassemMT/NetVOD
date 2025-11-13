<?php
declare(strict_types= 1);
namespace netvod\action;

use netvod\exception\BadRequestMethodException;
use netvod\exception\MissingArgumentException;
use netvod\token\TokenManager;

class VerifierMailAction implements Action {
    public function execute(): string {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            if (isset($_GET["token"])) {
                if (TokenManager::useToken($_GET["token"])) {
                    return "Email verified successfully.";
                } else {
                    return "Invalid or expired token.";
                }
            } else throw new MissingArgumentException("token");
        } else if ($_SERVER["REQUEST_METHOD"] === "POST") {
            TokenManager::genererToken();
            return TokenManager::getToken();
        }else throw new BadRequestMethodException();
    }
}
