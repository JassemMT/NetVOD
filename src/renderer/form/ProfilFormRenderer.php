<?php
declare(strict_types= 1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class ProfilFormRenderer implements Renderer {
    public function render(): string {
        return <<<FIN
        <form method="post" action="?action=profil-info">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" />
            <br/>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" />
            <br/>
            <button type="submit">Enregistrer</button>
        </form>
        FIN;
    }
}