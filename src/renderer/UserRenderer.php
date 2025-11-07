<?php
declare(strict_types=1);
namespace netvod\renderer;

class UserRenderer implements Renderer {

    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public static function render(array $params = []): string {
        $user = $this->user;
        return <<<FIN
        <div class="user">
            <p>{$user->email}</p>
        </div>
        FIN;
    }

}