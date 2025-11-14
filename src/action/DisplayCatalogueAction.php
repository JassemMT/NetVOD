<?php
declare(strict_types=1);
namespace netvod\action;

use InvalidArgumentException;
use netvod\exception\AuthnException;
use netvod\exception\AuthzException;
use netvod\repository\SerieRepository;
use netvod\renderer\ListeProgrammeRenderer;
use netvod\exception\BadRequestMethodException;
use netvod\auth\AuthnProvider;
use netvod\auth\AuthzProvider;

class DisplayCatalogueAction implements Action {

    public function execute(): string {
        if (!AuthnProvider::isLoggedIn()) throw new AuthnException("Il faut être connecté pour voir le catalogue");
        if (!AuthzProvider::isVerified()) throw new AuthzException("Il faut avoir vérifié son compte pour voir le catalogue");

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new BadRequestMethodException();
        }

        // Récupère le filtre éventuel
        $filtre = $_GET['filtre'] ?? null;

        $keyword = $_GET['q'] ?? null;


        // HTML des boutons
        $action_param = 'action=display-catalogue'; 

        $html = <<<FIN
        <br>
        <div class="catalog-toolbar container" role="toolbar" aria-label="Contrôles du catalogue">
            <div class="toolbar-actions" role="group" aria-label="Filtres">
                <a href="?{$action_param}" class="btn">Vue normale</a>
                <a href="?{$action_param}&filtre=genre" class="btn">Filtrer par genre</a>
                <a href="?{$action_param}&filtre=public" class="btn">Filtrer par public</a>
            </div>

            <form method="get" action="" class="search-form" role="search" aria-label="Recherche dans le catalogue">
                <input type="hidden" name="action" value="display-catalogue">
                <div class="form-row search-row">
                    <label for="catalog-search" class="visually-hidden">Rechercher</label>
                    <input id="catalog-search" name="q" type="search" class="form-input" placeholder="Rechercher..." aria-label="Rechercher le catalogue" required>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </div>
        <br><hr/><br><br>
        FIN;
        // ======================================================
        // CAS 0 — Recherche par mots-clés (PRIORITAIRE)
        // ======================================================
        if (!empty($keyword)) {
            $catalogue = SerieRepository::search($keyword);
            $html .= "<h2>Résultats pour : ".htmlspecialchars($keyword)."</h2>";

            $renderer = new ListeProgrammeRenderer($catalogue);
            return $html . $renderer->render();
        }
        


        // ========================
        // CAS 1 — Vue classique
        // ========================
        if ($filtre === null) {
            $catalogue = SerieRepository::findAll();
            $renderer = new ListeProgrammeRenderer($catalogue);
            return $html . $renderer->render();
        }

        // ========================
        // CAS 2 — Filtrer par GENRE
        // ========================
        if ($filtre === 'genre') {
            $genres = SerieRepository::findAllGenres();
            $html .= '<h2>Catalogue trié par genre</h2>';
            foreach ($genres as $g) {
                $catalogueGenre = SerieRepository::findAll($g, null);
                if ($catalogueGenre->getProgrammes()) {
                    $renderer = new ListeProgrammeRenderer($catalogueGenre);
                    $html .= "<h3 style='margin-top: 30px;'>$g</h3>";
                    $html .= $renderer->render();
                }
            }
            return $html;
        }

        // ========================
        // CAS 3 — Filtrer par PUBLIC
        // ========================
        if ($filtre === 'public') {
            $publics = SerieRepository::findAllPublics();
            $html .= '<h2>Catalogue trié par public</h2>';
            foreach ($publics as $p) {
                $cataloguePublic = SerieRepository::findAll(null, $p);
                if ($cataloguePublic->getProgrammes()) {
                    $renderer = new ListeProgrammeRenderer($cataloguePublic);
                    $html .= "<h3 style='margin-top: 30px;'>$p</h3>";
                    $html .= $renderer->render();
                }
            }
            return $html;
        }
        

        // Si le filtre est invalide
        throw new InvalidArgumentException("filtre");
    }
}