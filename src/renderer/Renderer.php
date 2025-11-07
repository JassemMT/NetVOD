<?php
declare(strict_types=1);
namespace netvod\renderer;

interface Renderer {

    public function render(): string;

}