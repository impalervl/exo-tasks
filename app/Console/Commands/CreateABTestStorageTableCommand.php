<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Core\Console\Contracts\CommandInterface;
use Core\Services\DatabaseService;

class CreateABTestStorageTableCommand implements CommandInterface
{
    private DatabaseService $dbService;

    public function __construct()
    {
        $this->dbService = DatabaseService::getInstance();
    }

    public function handle(array $args): void
    {
        $this->dbService->execute("
            CREATE TABLE IF NOT EXISTS ab_tests_counts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                promotion_id INT NOT NULL,
                design_id INT NOT NULL,
                count INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT uc_promotion_user UNIQUE (promotion_id, design_id)
            )
        ");

        echo 'Created A/B test storage table successfully.' . PHP_EOL;
    }

    public function getName(): string
    {
        return 'create-ab-test-storage-table';
    }
}
