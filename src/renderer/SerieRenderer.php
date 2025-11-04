<?php
declare(strict_types= 1);
namespace netvod\renderer;

class SerireRenderer implements Renderer {
    public function render(array $params = []): string {
        return "<div>Serie Renderer Output</div>"; //temporaire
    }
}