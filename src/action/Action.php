<?php
declare(strict_types= 1);
namespace netvod\action;

interface Action
{
    public static function execute(): string;

}