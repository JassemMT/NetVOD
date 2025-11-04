<?php
declare(strict_types=1);
namespace netvod\exception;

class InvalidArgumentException extends \Exception
{
    public function __construct($argument)
    {
        parent::__construct("L'argument '$argument' est invalide");
    }
}