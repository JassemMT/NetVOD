<?php
declare(strict_types=1);
namespace netvod\exception;

class InvalidPropertyValueException extends \Exception
{
    public function __construct($propertyName, $value)
    {
        parent::__construct("Valeur invalide pour la propriété '$propertyName' : $value");
    }
}