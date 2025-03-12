<?php
namespace App;

use PDO;
use PDOException;

class Database {
    private $pdo;

    public function __construct(array $dbConfig) {
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";
        try {
            $this->pdo = new PDO(
                $dsn, 
                $dbConfig['user'], 
                $dbConfig['pass'], 
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}