<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\auth\AuthnProvider;
use netvod\exception\ActionUnauthorizedException;
use netvod\exception\AuthException;

class DefaultAction implements Action {
    public function execute() : string {
        if (AuthnProvider::isLoggedIn()) {
            return '
            <h1>Bienvenue sur NetVOD</h1>
            <p>Votre plateforme de streaming préférée !</p> ';
        } else {
            header('Location: ?action=login');
            return "";
        }
    }
}