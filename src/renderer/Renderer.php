<?php
declare(strict_types=1);
namespace netvod\renderer;

interface Renderer {
    public static function render(array $params = []): string; //l'attribut $params est un tableau associatif contenant les données nécessaires au rendu (si besoin)
}