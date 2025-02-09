<?php

declare(strict_types=1);

namespace Core\Services\Drivers\Database;

use PDO;

interface DatabaseDriverInterface
{
    public function getConnection(): PDO;

    public function query(string $sql, array $params = []): array;

    public function count(string $table, string $column = '*'): int;

    public function execute(string $sql, array $params = []): bool;

    public function insert(string$table, array $data): bool;

    public function update(string $table, array $data, array $conditions): bool;

    public function delete(string $table, array $conditions): bool;
}
