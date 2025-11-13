<?php
declare(strict_types= 1);
namespace netvod\auth;

use netvod\repository\UserRepository;

class AuthzProvider {

    public static function isVerified(): bool {
        $id = AuthnProvider::getSignedInUser();
        return UserRepository::getVerifier($id);
    }

    public static function validationVerifier(): void {
        UserRepository::setVerifier(AuthnProvider::getSignedInUser(), true);
    }

}