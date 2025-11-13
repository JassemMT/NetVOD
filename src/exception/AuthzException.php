<?php
declare(strict_types=1);
namespace netvod\exception;

class AuthzException extends \Exception
{
    public function __construct($propertyName)
    {
        parent::__construct($propertyName);
    }
}