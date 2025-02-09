<?php

declare(strict_types=1);

namespace Core\Services;

use Core\Helpers\Config;
use Core\Services\Drivers\Database\DatabaseDriverInterface;
use Core\Services\Drivers\Database\MySqlDriver;
use InvalidArgumentException;
use PDO;

class DatabaseService
{
    private static ?self $instance = null;

    private DatabaseDriverInterface $driver;

    private function __construct()
    {
        $this->resolveDriver();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->driver->getConnection();
    }

    public function query(string $sql, array $params = []): array
    {
        return $this->driver->query($sql, $params);
    }

    public function execute(string $sql, array $params = []): bool
    {
        return $this->driver->execute($sql, $params);
    }

    public function select(
        string $table,
        array $columns = [],
        array $joins = [],
        array $conditions = [],
        string $order = '',
        ?int $limit = null
    ): array {
        return $this->driver->select($table, $columns, $joins, $conditions, $order, $limit);
    }

    public function count(string $table, string $column = '*'): int
    {
        return $this->driver->count($table, $column);
    }

    public function insert(string $table, array $data): bool
    {
        return $this->driver->insert($table, $data);
    }

    public function update(string $table, array $data, array $conditions): bool
    {
        return $this->driver->update($table, $data, $conditions);
    }

    public function delete(string $table, array $conditions): bool
    {
        return $this->driver->delete($table, $conditions);
    }

    private function resolveDriver(): void
    {
        $driver = Config::get('db.default') ?? '';

        $this->driver = match ($driver) {
            'mysql' => new MySqlDriver(),
            default => throw new InvalidArgumentException("Unsupported database driver: $driver"),
        };
    }
}
