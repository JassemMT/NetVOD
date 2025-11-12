<?php

namespace netvod\action;
use netvod\auth\AuthnProvider;

class LogOutAction implements Action
{    public function execute(): string
    {
        // Properly destroy the session for logout
        session_unset(); // Remove all session variables
        session_destroy(); // Destroy the session
        header('Location: ?action=login');
        return "";
    }
}