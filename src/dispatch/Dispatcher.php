<?php

namespace netvod\dispatch;

use netvod\action as act;

class Dispatcher
{
    private string $action;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function run(): void
    {

        // Liste des actions nécessitant d'être connecté
        $privateActions = [
            'add-serie',
            'notation',
            'display-user',
            'display-list'
        ];

        // Si on essaie d'accéder à une action privée sans être connecté → redirection
        if (in_array($this->action, $privateActions) && !isset($_SESSION['user'])) {
            $obj = new act\SignInAction();
        } else {
            switch ($this->action) {
                case 'login':
                    $obj = new act\LoginAction();
                    break;
                case 'register':
                    $obj = new act\RegisterAction();
                    break;
                case 'display-catalogue':
                    $obj = new act\DisplayCatalogueAction();
                    break;
                case 'display-serie':
                    $obj = new act\DisplaySerieAction();
                    break;
                case 'display-episode':
                    $obj = new act\DisplayEpisodeAction();
                    break;
                case 'add-serie':
                    $obj = new act\AddSerieAction();
                    break;
                case 'notation':
                    $obj = new act\NotationAction();
                    break;
                case 'display-user':
                    $obj = new act\DisplayUserAction();
                    break;
                case 'display-liste':
                    $obj = new act\DisplayListeProgrammeAction();
                    break;
                case 'logout':
                    $obj = new act\LogOutAction();
                    break;
                default:
                    $obj = new act\DefaultAction();
                    break;
            }
        }

        $html = $obj->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        $title = 'NetVOD App';

        $menu = '<nav><a href="?action=default">Accueil</a> | ';

        if (isset($_SESSION['user'])) {
            $menu .= '<a href="?action=display-catalogue">Catalogue</a> | 
                      <a href="?action=display-list">Mes séries</a> | 
                      <a href="?action=display-user">Mon profil</a> |
                      <a href="?action=logout">Déconnexion</a></nav>';
        } else {
            $menu .= '<a href="?action=login">Connexion</a> |
                      <a href="?action=register">Créer un compte</a></nav>';
        }

        echo <<<HTML
        <!doctype html>
        <html lang="fr">
        <head>
        <meta charset="utf-8">
        <title>{$title}</title>
        <style>
            body { font-family:Arial, sans-serif; margin:18px; }
            nav a { margin-right:12px; }
        </style>
        </head>
        <body>
        <header><h1>{$title}</h1>{$menu}<hr></header>
        <main>{$html}</main>
        </body>
        </html>
        HTML;
    }
}
