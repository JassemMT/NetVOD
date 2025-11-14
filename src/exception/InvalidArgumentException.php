<?php
declare(strict_types=1);
namespace netvod\exception;

class InvalidArgumentException extends \Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }
}