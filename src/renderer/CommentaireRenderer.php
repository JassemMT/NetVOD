<?php
declare(strict_types= 1);
namespace netvod\renderer;

use netvod\repository\SerieRepository;

class CommentaireRenderer implements Renderer {

    private int $serie_id;

    public function __construct(int $id) {
        $this->serie_id = $id;
    }

    public function render(): string {
        
        $comments = SerieRepository::getComments($this->serie_id);
        $html = '';

        foreach ($comments as $comment) {
            $html .= <<<FIN
            <div class="commentaire">
                <p>{$comment['note']}</p>
                <p>{$comment['contenu']}</p>
                <p>{$comment['date']}</p>
            </div>
            FIN;
        }

        return $html;
    }
}