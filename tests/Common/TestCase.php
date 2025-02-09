<?php

declare(strict_types=1);

namespace Tests\Common;

use Core\Services\DatabaseService;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestCase extends BaseTestCase
{
    public DatabaseService $db;

    public TestClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestClient();
        $this->db     = DatabaseService::getInstance();
    }

    public function get(string $uri, array $queryParams = []): Response
    {
        return $this->client->get($uri, $queryParams);
    }

    public static function tearDownAfterClass(): void
    {
        self::dropAllTables();
    }

    // Function to drop all tables in the database
    public static function dropAllTables(): void
    {
        $db     = DatabaseService::getInstance();
        $tables = $db->getConnection()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        $db->execute('SET foreign_key_checks = 0;');
        foreach ($tables as $table) {
            $db->execute("DROP TABLE IF EXISTS {$table}");
        }
        $db->execute('SET foreign_key_checks = 1;');
    }
}
