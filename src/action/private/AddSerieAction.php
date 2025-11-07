<?php
declare(strict_types= 1);
namespace netvod\action\private;

use netvod\action\Action;
use netvod\classes\Serie;
use netvod\exception\BadRequestMethodException;
use netvod\exception\InvalidArgumentException;
use netvod\exception\MissingArgumentException;
use netvod\repository\SerieRepository;

class AddSerieAction implements Action {



    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Formulaire simple pour ajouter une série
            return <<<HTML
            <div class="container">
                <h1>Ajouter une série</h1>
                <form action="?action=addserie" method="post" class="form-add-serie">
                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" id="titre" name="titre" required maxlength="255" />
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required maxlength="2000"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="annee">Année</label>
                        <input type="number" id="annee" name="annee" min="1800" max="2100" required />
                    </div>

                    <div class="form-group">
                        <label for="image">URL image</label>
                        <input type="url" id="image" name="image" required />
                    </div>

                    <button type="submit" class="btn-primary">Créer la série</button>
                </form>
            </div>
            HTML;
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Champs obligatoires
            if (!isset($_POST['titre']) || !isset($_POST['description']) || !isset($_POST['annee']) || !isset($_POST['image'])) {
                throw new MissingArgumentException('Tous les champs sont requis pour ajouter une série.');
            }

            $titre = trim((string)($_POST['titre'] ?? ''));
            $description = trim((string)($_POST['description'] ?? ''));
            $anneeRaw = $_POST['annee'] ?? '';
            $image = trim((string)($_POST['image'] ?? ''));

            if ($titre === '' || $description === '' || $image === '') {
                throw new InvalidArgumentException('Les champs ne peuvent pas être vides.');
            }

            if (!is_numeric($anneeRaw)) {
                throw new InvalidArgumentException('Année invalide.');
            }

            $annee = (int)$anneeRaw;
            if ($annee < 1800 || $annee > (int)date('Y') + 5) {
                throw new InvalidArgumentException('Année hors des limites autorisées.');
            }

            // Création de l'objet Serie
            $serie = new Serie($titre, $description, $annee, $image);

            // Insertion via le repository
            $repo = SerieRepository::getInstance();

            try {
                $id = $repo->addSerie($serie);
                // Retourner vide (le dispatcher ou le renderer redirigera ou affichera la liste)
                return '';
            } catch (\Exception $e) {
                throw new \RuntimeException('Impossible d\'ajouter la série : ' . $e->getMessage());
            }

        } else {
            throw new BadRequestMethodException();
        }
    }
}