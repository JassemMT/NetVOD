<?php
declare(strict_types= 1);
namespace iutnc\deefy\handler;

use netvod\exception as exc;

class ExceptionHandler {
    
    public static function handle(array $callback): void {
        try {
            call_user_func($callback);
        } catch (exc\AuthException $e) {
            echo "exception d'authentification: " . $e->getMessage();
            return;
        } catch (exc\InvalidArgumentException $e) {
            echo "argument invalide: " . $e->getMessage();
            return;
        } catch (exc\MissingArgumentException $e) {
            echo "argument manquant: " . $e->getMessage();
            return;
        } catch (exc\BadRequestMethodException $e) {
            echo "methode de requete invalide: " . $e->getMessage();
            return;
        } catch (exc\ActionUnauthorizedException $e) {
            echo "permission refusée: " . $e->getMessage();
            return;
        } catch (\PDOException $e) {
            echo "erreur de la base de données: " . $e->getMessage();
            return;
        }
    }
    
}