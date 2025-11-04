<?php
declare(strict_types=1);
namespace netvod\exception;

class MissingArgumentException extends \Exception
{
    public function __construct($argument)
    {
        parent::__construct("l'argument '$argument' est manquant");
    }
}