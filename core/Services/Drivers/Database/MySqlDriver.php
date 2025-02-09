<?php
declare(strict_types=1);

namespace Core\Services\Drivers\Database;

use Core\Helpers\Config;
use InvalidArgumentException;
use PDO;
use PDOException;

class MySqlDriver implements DatabaseDriverInterface
{
    private PDO $connection;

    public function __construct()
    {
        $this->connect();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function select(
        string $table,
        array $columns = [],
        array $joins = [],
        array $conditions = [],
        string $order = '',
        ?int $limit = null,
    ): array {

        $params = [];

        if (count($columns)) {
            $columns = implode(', ', $columns);
        } else {
            $columns = '*';
        }

        $sql = "SELECT {$columns} FROM {$table}";

        if (!empty($joins)) {
            $sql = $this->applyJoins($sql, $joins);
        }

        if (!empty($conditions)) {
            $result = $this->applyConditions($sql, $conditions);
            $sql    = $result['sql'];
            $params = $result['params'];
        }

        if ($order !== '') {
            $sql .= " ORDER BY {$order}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->query($sql, $params);
    }

    public function count(string $table, string $column = '*'): int
    {
        $sql  = "SELECT COUNT({$column}) FROM {$table}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

    public function insert(string $table, array $data): bool
    {
        $count      = count($data);
        $columns    = array_keys($data[0]);
        $columnList = implode(',', $columns);

        $rowPlaceholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
        $placeholders    = implode(',', array_fill(0, $count, $rowPlaceholders));
        $sql             = "INSERT INTO $table ($columnList) VALUES $placeholders";

        $flattenedValues = [];
        foreach ($data as $row) {
            $flattenedValues = array_merge($flattenedValues, array_values($row));
        }

        return $this->execute($sql, $flattenedValues);
    }

    public function update(string $table, array $data, array $conditions): bool
    {
        $setClause = implode(',', array_map(fn($key) => "$key = ?", array_keys($data)));
        $conditionClause = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));

        $sql = "UPDATE $table SET $setClause WHERE $conditionClause";
        return $this->execute($sql, array_merge(array_values($data), array_values($conditions)));
    }

    public function delete(string $table, array $conditions): bool
    {
        $conditionClause = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));
        $sql = "DELETE FROM $table WHERE $conditionClause";
        return $this->execute($sql, array_values($conditions));
    }

    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                Config::get('db.mysql.host'),
                Config::get('db.mysql.port'),
                Config::get('db.mysql.dbname'),
                Config::get('db.mysql.charset'),
            );

            $this->connection = new PDO(
                $dsn,
                Config::get('db.mysql.username'),
                Config::get('db.mysql.password'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage() . PHP_EOL);
        }
    }

    private function applyJoins(string $sql, array $joins): string
    {
        foreach ($joins as $join) {
            $type = isset($join['type']) ? strtoupper($join['type']) : 'INNER';

            $allowedJoinTypes = ['INNER', 'LEFT', 'RIGHT', 'FULL', 'LEFT OUTER', 'RIGHT OUTER', 'FULL OUTER'];
            if (!in_array($type, $allowedJoinTypes)) {
                $type = 'INNER';
            }
            $joinTable = $join['table'];
            $on = $join['condition'];
            $sql .= " {$type} JOIN {$joinTable} ON {$on}";
        }

        return $sql;
    }

    private function applyConditions(string $sql, array $conditions): array
    {
        $clauses = [];
        $params  = [];

        foreach ($conditions as $key => $value) {
            if (is_array($value) && count($value) === 3) {
                list($column, $operator, $val) = $value;
            } else {
                $column = $key;
                $val = $value;
                $operator = '=';
                if (str_contains($key, ' ')) {
                    list($column, $op) = explode(' ', trim($key), 2);
                    $operator = strtoupper(trim($op));
                }
            }

            $allowedOperators = ['=', '>', '<', '>=', '<=', 'LIKE'];

            if (!in_array($operator, $allowedOperators)) {
                throw new InvalidArgumentException("Unsupported operator: $operator");
            }

            if ($operator === 'LIKE') {
                $val = "%{$val}%";
            }

            $param = ':' . preg_replace('/[^a-zA-Z0-9_]/', '', $column) . count($params);

            $clauses[] = "{$column} {$operator} {$param}";
            $params[$param] = $val;
        }

        $sql .= " WHERE " . implode(' AND ', $clauses);

        return [
            'sql'    => $sql,
            'params' => $params
        ];
    }
}
