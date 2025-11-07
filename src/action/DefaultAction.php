<?php
declare(strict_types=1);
namespace netvod\action;

class DefaultAction implements Action {
    public function execute() : string {
        var_dump($_SESSION);

        return '
        <h1>Bienvenue sur NetVOD</h1>
        <p>Votre plateforme de streaming préférée !</p> ';
    }
}