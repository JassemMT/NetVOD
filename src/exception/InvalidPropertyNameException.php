<?php
declare(strict_types=1);
namespace netvod\exception;

class InvalidPropertyNameException extends \Exception
{
    public function __construct($propertyName)
    {
        parent::__construct("Propriété inconnue : '$propertyName'");
    }
}