<?php
declare(strict_types= 1);
namespace netvod\renderer;

use netvod\classes\Serie;

class SerieRenderer implements Renderer {
    public function render(array $params): string {
        serie = (Serie)$params["serie"];
        foreach ($serie->episodes as $episode) {
            // Render each episode (implementation not shown)
        }
    }
}