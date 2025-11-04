<?php
delclare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';
session_start(); // après l'autoload pour éviter les erreurs de classes non trouvées

use netvod\dispatcher\Dispatcher;

$dispatcher = new Dispatcher();
$dispatcher->run();