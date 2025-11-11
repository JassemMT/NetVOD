<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';
session_start(); // après l'autoload pour éviter les erreurs de classes non trouvées
// l'index est executé à chaque requête donc pas besion de session_start() ailleur qu'ici
// /!\ ne pas faire if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); } partout


use netvod\dispatch\Dispatcher;
use netvod\core\Database;
use netvod\handler\ExceptionHandler;


Database::setConfig( __DIR__ . '/config/configdb.ini');

$dispatcher = new Dispatcher($_GET['action'] ?? '');

ExceptionHandler::handle([$dispatcher, 'run']);
