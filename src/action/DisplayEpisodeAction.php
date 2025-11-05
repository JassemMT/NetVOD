<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\renderer\EpisodeRenderer;
use netvod\exception\BadRequestMethodException;

class DisplayEpisodeAction implements Action {
    public static function execute(): string {
        print("Affichage de l'épisode : <br>");

        $rep = EpisodeRepository::GetInstance();
        
        echo  <<<FIN

        FIN;
        // il faut déterminer comment récupérer la liste d'épisodes voulu par l'utilisateur 

        $pl = $rep->afficherSerie();
        var_dump($pl);

        $action = $_GET['action'] ?? null;
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

//         // === Page d'accueil ===
//         if (!$action) {
//             echo <<<OUI
//                 <!DOCTYPE html>
//                 <html lang="fr">
//                 <head>
//                     <meta charset="UTF-8">
//                     <title>Épisodes</title>
//                 </head>
//                 <body>
//                     <h1>Épisodes</h1>
//                     <p><a href="?action=liste">Afficher la liste des épisodes</a></p>
//                     <p>Ou cliquez sur un épisode :</p>
//                     <hr>
//                 OUI;

//             foreach ($episodes as $id => $titre) {
//                 $titreEsc = htmlspecialchars($titre);
//                 echo <<<NON
//                     <p><strong><a href="?action=episode&id=$id">Épisode #$id</a></strong><br>
//                     $titreEsc</p>
//                 NON;
//             }

//             echo <<<HTML
//                     </body>
//                     </html>
//                 HTML;
//             exit;
// }

//         // === Afficher un épisode ===
//              VA UTILISER FINDBYID(id ep) renvoyant un ep puis utiliser EpisodeRenderer->render((array)ep)
//         if ($action === 'episode' && $id && isset($episodes[$id])) {
//             $titre = htmlspecialchars($episodes[$id]);
//             echo <<<HTML
//                     <!DOCTYPE html>
//                     <html lang="fr">
//                     <head>
//                         <meta charset="UTF-8">
//                         <title>Épisode #$id</title>
//                     </head>
//                     <body>
//                         <h1>Épisode #$id</h1>
//                         <p><strong>Titre :</strong> $titre</p>
//                         <p><em>Lecture de la vidéo en cours...</em></p>
//                         <hr>
//                         <p><a href="episodes_no_css.php">Retour à la liste</a></p>
//                     </body>
//                     </html>
//                 HTML;
//             exit;
//         }

//         // === Liste complète ===    
            // va récupérer la liste avec EpisodeRepository->findBySerie() renvoyant une liste d'épisode

            // va utiliser EpisodeRenderer->render(lsEp)
//         if ($action === 'liste') {
//             echo <<<HTML
//                     <!DOCTYPE html>
//                     <html lang="fr">
//                     <head>
//                         <meta charset="UTF-8">
//                         <title>Liste des épisodes</title>
//                     </head>
//                     <body>
//                         <h1>Liste des épisodes</h1>
//                         <ul>
//                     HTML;

//             foreach ($episodes as $id => $titre) {
//                 $titreEsc = htmlspecialchars($titre);
//                 echo "<li><a href=\"?action=episode&id=$id\">Épisode #$id</a> : $titreEsc</li>";
//             }

//             echo <<<HTML
//                     </ul>
//                     <hr>
//                     <p><a href="episodes_no_css.php">Retour</a></p>
//                 </body>
//                 </html>
//             HTML;
//             exit;
//         }
    }
}