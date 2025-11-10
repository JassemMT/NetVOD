<?php
declare(strict_types=1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class NotationFormRenderer implements Renderer {

    protected int $idSerie;
    public function __construct(int $idSerie) {
        $this->idSerie = $idSerie;
    }
    public function render(): string {
        $id = $this->idSerie;
        return <<<FIN
                <section class="episode-note" aria-labelledby="note-title">
                    <h2 id="note-title">Noter</h2>

                    <form action="?action=notation&id={$id}" method="post" class="note-form" novalidate>
                        <div class="form-row">
                            <label for="commentaire">Commentaire</label>
                            <input type="text" id="commentaire" name="commentaire" placeholder="Votre commentaire" required />
                        </div>

                        <div class="form-row">
                            <label for="note">Note pour la série</label>
                            <input type="number" id="note" name="note" min="1" max="5" step="1" placeholder="1–5" required />
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </div>
                    </form>
                </section>
            FIN;
    }
}

