<?php

namespace netvod\dispatch;

use netvod\action as act;
use netvod\notification\Notification;

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
            $obj = new act\LogInAction();
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
                    $obj = new act\private\AddSerieAction();
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
                case 'add-favoris':
                    $obj = new act\AddFavorisAction(); // TODO: ne s'appelle qu'en post, l'argument est "serie_id"
                    break;
                case 'verify-mail':
                    $obj = new act\VerifierMailAction();
                    break;
                case 'profil-info':
                    $obj = new act\ChangerProfilInfoAction();
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

        $menu = '';
        
        if (isset($_SESSION['user'])) {
            $menu .= '<nav><a href="?action=default">Accueil</a> | ';
            $menu .= '<a href="?action=display-catalogue">Catalogue</a> | 
                      <a href="?action=display-liste">Mes séries</a> | 
                      <a href="?action=display-user">Mon profil</a> |
                      <a href="?action=logout">Déconnexion</a></nav>';
        } else {
            $menu .= '<a href="?action=login">Connexion</a> |
                      <a href="?action=register">Créer un compte</a></nav>';
        }

        $notification = Notification::render();

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$title}</title>
                <!-- import css  -->
                <link rel="stylesheet" href="ressources/css/app.bundle.css">
            </head>
            <body>
                <div class="app-wrapper">
                    <!-- HEADER -->
                    <header class="app-header" role="banner">
                        <div class="header-content">
                            <div class="logo">NetVOD</div>
                            <nav class="nav" role="navigation" aria-label="Menu principal">
                                {$menu}
                            </nav>
                        </div>
                    </header>

                    <!-- MAIN CONTENT -->
                    <main class="container">
                        {$html}
                    </main>
                </div>

                {$notification}

                <!-- Script -->
                <script src="ressources/js/app.bundle.js"></script>
            </body>
        </html>
        HTML;
    }
}
