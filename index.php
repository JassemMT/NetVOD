<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';
session_start(); // après l'autoload pour éviter les erreurs de classes non trouvées


use netvod\dispatch\Dispatcher;
use netvod\core\Database;


Database::setConfig( __DIR__ . '/config/configdb.ini');

$dispatcher = new Dispatcher($_GET['action'] ?? '');
$dispatcher->run();
