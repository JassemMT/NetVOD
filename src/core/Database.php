<?php
namespace netvod\core;

use PDO;

class Database {
    private \PDO $pdo;
    private static ?Database $instance = null;
    private static array $config = [];

    private function __construct( array $conf ){
        $this->pdo = new \PDO( $conf['dsn'], $conf['user'], $conf['pass'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function getInstance(){
        if (is_null(self::$instance)){
            self::$instance = new Database(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig( $file ){
        $conf = parse_ini_file( $file );

        if($conf===false){
            throw new \PDOException("Fichier de configuration introuvable");
        }

        $driver = $conf['driver'] ?? 'mysql';
        $host   = $conf['host'] ?? 'localhost';
        $dbname = $conf['database'] ?? '';
        if (!$dbname) {
            throw new \PDOException("Le fichier de configuration doit contenir 'database'.");
        }

        $dsn = "$driver:host=$host;dbname=$dbname";
        if (!empty($conf['charset'])) {
            $dsn .= ";charset={$conf['charset']}";
        }

        self::$config = [
            'dsn'  => $dsn,
            'user' => $conf['username'] ?? $conf['user'] ?? '',
            'pass' => $conf['password'] ?? $conf['pass'] ?? ''
        ];
    }

    // Getter pour accéder à l'objet PDO
    public function __get($name){
        if ($name === 'pdo') {
            return $this->pdo;
        }
        return null;
    }

    // Méthode explicite pour récupérer l'instance PDO (préférable à l'accès magique)
    public function getPdo(): \PDO {
        return $this->pdo;
    }
}
