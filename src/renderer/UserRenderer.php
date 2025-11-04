<?php
declare(strict_types=1);
namespace netvod\renderer;

class UserRenderer implements Renderer {
    public function render(array $params = []): string {
        return "<div>User Renderer Output</div>"; //temporaire
    }

}