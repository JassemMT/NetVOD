<?php
declare(strict_types=1);
namespace netvod\exception;

class ActionUnauthorizedException extends \Exception
{
    public function __construct($action)
    {
        parent::__construct("vous n'avez pas le droit ".$action);
    }
}