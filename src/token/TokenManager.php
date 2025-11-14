<?php
declare(strict_types= 1);
namespace netvod\token;

use netvod\auth\AuthnProvider;
use netvod\auth\AuthzProvider;
use netvod\exception\AuthnException;
use netvod\repository\TokenRepository;

class TokenManager {
    
    public static function genererToken(int $life_time = 300) : void {
        if (AuthnProvider::isLoggedIn()) {
            $token = bin2hex(random_bytes(32));
            TokenRepository::setToken(AuthnProvider::getSignedInUser(), $token, time(), $life_time);
        } else throw new AuthnException("Utilisateur non authentifié");
    }

    public static function checkToken() : bool {
        if (AuthnProvider::isLoggedIn()) {
            $id = AuthnProvider::getSignedInUser();
            if (TokenRepository::hasToken($id)) {
                $token = TokenRepository::getToken($id);
                $created_at = $token['created_at'];
                $current_time = time();
                if (($current_time - $created_at) <= $token['life_time']) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else throw new AuthnException("Utilisateur non authentifié");
    }

    public static function useToken(string $token) : bool {
        $id = AuthnProvider::getSignedInUser();
        if (self::checkToken()) {
            $token_db = TokenRepository::getToken($id);
            if (isset($token_db['token']) && hash_equals($token_db['token'], $token)) {
                TokenRepository::deleteToken($id);
                AuthzProvider::validationVerifier();
                return true;
            } else {
                return false;
            }
        } else {
            TokenRepository::deleteToken($id);
            return false;
        }
    }

    public static function getToken() : ?string {
        if (AuthnProvider::isLoggedIn()) {
            if (TokenRepository::hasToken(AuthnProvider::getSignedInUser())) {
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
                $url .= $_SERVER['HTTP_HOST'];
                $url .= explode("?", $_SERVER['REQUEST_URI'])[0]; //on ne veut pas les anciens paramètres GET
                $value = TokenRepository::getToken(AuthnProvider::getSignedInUser())['token'];
                $url .= "?action=verify-mail&token={$value}";
                return $url;
            } else {
                return null;
            }
        } else throw new AuthnException("Utilisateur non authentifié");
    }


}
