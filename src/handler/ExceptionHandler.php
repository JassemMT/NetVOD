<?php
declare(strict_types= 1);
namespace netvod\handler;

use netvod\exception as exc;
use netvod\notification\Notification;

class ExceptionHandler {
    
    public static function handle(array $callback): void {
        try {
            call_user_func($callback);
        } catch (exc\AuthnException $e) {
            echo "exception d'authentification: " . $e->getMessage();
            Notification::save($e->getMessage(), "Erreur", Notification::TYPE_ERROR);
            header("location: ?action=login"); //redirection vers la page de connexion
            return;
        } catch (exc\AuthzException $e) {
            echo "exception d'autorization: " . $e->getMessage();
            Notification::save($e->getMessage(), "Erreur", Notification::TYPE_ERROR);
            header("location: ?action=display-user");
            return;
        } catch (exc\InvalidArgumentException $e) {
            echo "argument invalide: " . $e->getMessage();
            Notification::save($e->getMessage(), "Erreur", Notification::TYPE_ERROR);
            header("location: {$_SERVER["REQUEST_URI"]}");
            return;
        } catch (exc\MissingArgumentException $e) {
            echo "argument manquant: " . $e->getMessage();
            Notification::save($e->getMessage(), "Erreur", Notification::TYPE_ERROR);
            header("location: {$_SERVER["REQUEST_URI"]}");
            return;
        } catch (exc\BadRequestMethodException $e) {
            echo "methode de requete invalide: " . $e->getMessage();
            Notification::save($e->getMessage(), "Erreur", Notification::TYPE_ERROR);
            header("location: {$_SERVER["REQUEST_URI"]}");
            return;
        } catch (\PDOException $e) {
            echo "erreur de la base de données: " . $e->getMessage();
            Notification::save($e->getMessage(), "Erreur", Notification::TYPE_ERROR);
            header("location: {$_SERVER["REQUEST_URI"]}");
            return;
        }
    }
    
}