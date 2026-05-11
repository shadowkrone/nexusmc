<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOStatement;

class Database {
    private PDO $pdo;

    public function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): array|false {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): int {
        $cols   = implode(', ', array_map(fn($k) => "`{$k}`", array_keys($data)));
        $places = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO `{$table}` ({$cols}) VALUES ({$places})", array_values($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $set  = implode(', ', array_map(fn($k) => "`{$k}` = ?", array_keys($data)));
        $stmt = $this->query("UPDATE `{$table}` SET {$set} WHERE {$where}", [...array_values($data), ...$whereParams]);
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int {
        return $this->query("DELETE FROM `{$table}` WHERE {$where}", $params)->rowCount();
    }

    public function count(string $table, string $where = '1', array $params = []): int {
        $row = $this->fetch("SELECT COUNT(*) AS c FROM `{$table}` WHERE {$where}", $params);
        return (int) ($row['c'] ?? 0);
    }

    public function getPdo(): PDO { return $this->pdo; }

    public function prefix(string $table): string {
        return DB_PREFIX . $table;
    }
}
