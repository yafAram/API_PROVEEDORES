<?php
namespace App;

use PDO;
use PDOException;

class Database {
    private $pdo;
    
    public function __construct(array $config) {
        $db = $config['db'];
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
        try {
            $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
