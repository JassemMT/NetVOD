<?php
declare(strict_types= 1);
namespace netvod\renderer\form;

use netvod\renderer\Renderer;

class ProfilFormRenderer implements Renderer {
    public function render(): string {
        return <<<FIN
        <section class="profile-edit container" aria-labelledby="profile-edit-title">
            <header>
                <h1 id="profile-edit-title">Modifier mes informations</h1>
            </header>

            <form method="post" action="?action=profil-info" class="profile-edit-form" novalidate>
                <div class="form-row">
                    <label for="nom">Nom</label>
                    <input id="nom" name="nom" type="text" />
                </div>

                <div class="form-row">
                    <label for="prenom">Prénom</label>
                    <input id="prenom" name="prenom" type="text" />
                </div>

                <div class="form-actions" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a class="btn" href="?action=profile">Annuler</a>
                </div>
            </form>
        </section>
        FIN;
    }
}