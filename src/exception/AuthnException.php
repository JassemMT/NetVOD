<?php
declare(strict_types=1);

namespace netvod\exception;

class AuthnException extends \Exception {
    public const INVALID_CREDENTIALS = 1;
    public const USER_NOT_FOUND = 2;

    public function __construct($propertyName)
    {
        parent::__construct($propertyName);
    }
}
