<?php
declare(strict_types=1);
namespace netvod\action;

use netvod\exception\MissingArgumentException;
use netvod\exception\InvalidArgumentException;

class NotationAction implements Action {

    public static function execute(){

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<FIN
                    <h1>Notation</h1>
                    <form action="?action=notation" method="post" class="note-form">
                        <label for="mail">Adresse mail</label>
                        <input type="email" id="mail" placeholder="exemple@mail.com" name="mail" required>

                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" placeholder="••••••••" name="password" required>

                        <button type="submit" class="btn-primary">Se connecter</button>
                    </form>
                    FIN;
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['mail'])) {
                if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL) !== null) {
                    $mail = $_POST['mail'];
                    AuthnProvider::signin($mail, $_POST['password']);
                    return "";
                } else throw new InvalidArgumentException("email");
            } else throw new MissingArgumentException("email");
        } else throw new BadRequestMethodException();
    
        // on récupère l'id de la série en session ou via le SerieRepository
        // $Sid = (int)$_SESSION['idSerie'];     

        // $Srep = SerieRepository::GetInstance  
        // on récupère la série avec l'id correspondant 
        $Serie = $Srep->findById($Sid);
        
        
        // on récupère les pistes de la playlist via son id
        $ListeSerie = $Srep->findAll();

        $optionsSerie = '';

        // on affiche chaque titre de la playlist en utilisant la méthod htmlspecialchars pour éviter les injections et autres tentatives
        // d'exécution de code dans le programme
        foreach ($ListeSerie as $s) {
            $DescriptionAnnee = $s['description'] ? ' - '.htmlspecialchars($s['annee']) : '';
            $optionsSerie .= "<li><strong>".htmlspecialchars($s['titre'])."</strong>{$artist} (".gmdate('i:s',(int)$s['duree']).")</li>";
        }

        // on affiche ensuite la playlist avec les pistes qu'elle contient
        echo <<<FIN
            <!DOCTYPE html>
            <html><head><meta charset="utf-8"><title>{$pl->nom}</title></head><body>
            <h1>{$pl->nom}</h1>
            <a href="index.php">Accueil</a> | <a href="playlists.php">Playlists</a> | <a href="logout.php">Déconnexion</a>
            <hr>
            <ul>{$trackList}</ul>
            <a href="add_track.php">Ajouter une piste</a> | <a href="playlists.php">Retour</a>
            </body></html>
        FIN;

        <?php
// choix_option.php

$options = [
    'episode' => 'Afficher un épisode',
    'liste'   => 'Afficher la liste des épisodes',
    'profil'  => 'Voir mon profil',
    'deconnexion' => 'Se déconnecter'
];

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $choix = $_POST['action'] ?? '';

    switch ($choix) {
        case 'episode':
            $message = "Vous avez choisi : Afficher un épisode";
            break;
        case 'liste':
            $message = "Vous avez choisi : Afficher la liste des épisodes";
            break;
        case 'profil':
            $message = "Vous avez choisi : Voir mon profil";
            break;
        case 'deconnexion':
            $message = "Vous avez choisi : Se déconnecter";
            break;
        default:
            $message = "Option invalide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix d'option</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .form-group { margin: 15px 0; }
        select, button { padding: 8px; font-size: 16px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 5px; }
    </style>
</head>
<body>

<h1>Choisissez une action</h1>

<form method="POST">
    <div class="form-group">
        <label for="action"><strong>Option :</strong></label><br>
        <select name="action" id="action" required>
            <option value="">-- Choisissez --</option>
            <?php foreach ($options as $value => $label): ?>
                <option value="<?= $value ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit">Valider</button>
</form>

<?php if ($message): ?>
    <div class="result">
        <strong><?= $message ?></strong>
    </div>
<?php endif; ?>

</body>
</html>


    }    
}