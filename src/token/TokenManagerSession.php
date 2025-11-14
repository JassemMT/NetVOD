<?php
/*
declare(strict_types= 1);
namespace netvod\token;

use netvod\auth\AuthnProvider;
use netvod\auth\AuthzProvider;
use netvod\exception\AuthnException;

class TokenManager {
    
    public static function genererToken(int $life_time = 300) : void {
        if (AuthnProvider::isLoggedIn()) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['token'] = ["value" => $token, "created_at" => time(), "life_time" => $life_time];
        } else throw new AuthnException("Utilisateur non authentifié");
    }

    public static function checkToken() : bool {
        if (AuthnProvider::isLoggedIn()) {
            if (isset($_SESSION['token'])) {
                $token = $_SESSION['token'];
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
        if (self::checkToken()) {
            if (isset($_SESSION['token']['value']) && hash_equals($_SESSION['token']['value'], $token)) {
                unset($_SESSION['token']);
                AuthzProvider::validationVerifier();
                return true;
            } else {
                return false;
            }
        } else {
            unset($_SESSION['token']);
            return false;
        }
    }

    public static function getToken() : ?string {
        if (AuthnProvider::isLoggedIn()) {
            if (isset($_SESSION['token'])) {
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
                $url .= $_SERVER['HTTP_HOST'];
                $url .= explode("?", $_SERVER['REQUEST_URI'])[0]; //on ne veut pas les anciens paramètres GET
                $url .= "?action=verify-mail&token={$_SESSION['token']['value']}";
                return $url;
            } else {
                return null;
            }
        } else throw new AuthnException("Utilisateur non authentifié");
    }


}
*/
