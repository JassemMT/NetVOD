<?php
declare(strict_types=1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class NotationFormRenderer implements Renderer {
    public static function render(array $data = []): string {
        $idEpisode = $data['id'];

        return <<<FIN
                <h1>Noter</h1>
                <form action="?action=noter&id={$idEpisode}" method="post" class="note-form">
                    <label for="titre">Titre de  la série</label>
                    <input type="text" id="titre" placeholder="Nom Série" name="titre" required>

                    <label for="commentaire">commentaire</label>
                    <input type="text" id="commentaire" placeholder="commentaire" name="commentaire" required>

                    <label for="note">Note pour la série</label>
                    <input type="int" min="1" max="5" step="1" id="note" placeholder="Note" name="note" required>

                    <button type="submit" class="btn-primary">Envoyer</button>
                </form>
            FIN;
    }
}