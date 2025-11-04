<?php
declare(strict_types=1);
namespace netvod\renderer;

class UserRenderer implements Renderer {
    public function render(array $params = []): string {
        $user = $params["user"];
        return <<<FIN
        <div class="user">
            <p>{$user->email}</p>
        </div>
        FIN;
    }

}