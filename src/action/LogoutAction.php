<?php

namespace netvod\action;
use netvod\auth\AuthnProvider;

class LogOutAction implements Action
{
    public function execute(): string
    {
        AuthnProvider::requireLogin();
        // Properly destroy the session for logout
        session_unset(); // Remove all session variables
        session_destroy(); // Destroy the session
        return '<p>Vous êtes déconnecté.
                <a href="?action=default">Retour à l\'accueil</a></p>';
    }
}