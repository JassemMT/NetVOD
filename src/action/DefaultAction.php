<?php
declare(strict_types=1);
namespace netvod\action;

class DefaultAction implements Action {
    public static function execute() : string {
        return <<<FIN
        <h1>Bienvenue sur NetVOD</h1>
        <p>Votre plateforme de streaming préférée !</p>
        FIN;
    }
}