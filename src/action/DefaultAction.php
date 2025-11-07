<?php
declare(strict_types=1);
namespace netvod\action;

class DefaultAction implements Action {
    public function execute() : string {
        $pseudo = $_SESSION['user']->email ?? 'Invité';

        return '
        <h1>Bienvenue sur NetVOD</h1>
        '.$pseudo. '
        <p>Votre plateforme de streaming préférée !</p> ';
    }
}