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
            <article class="commentaire" role="article" aria-label="Commentaire">
                <header class="comment-meta">
                    <span class="comment-note">{$comment['note']}</span>
                    <time class="comment-date" datetime="{$comment['date']}">{$comment['date']}</time>
                </header>

                <div class="comment-body">
                    {$comment['contenu']}
                </div>
            </article>
            FIN;
        }

        return $html;
    }
}