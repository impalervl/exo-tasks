<?php

declare(strict_types=1);

namespace App\Services\ABTest\Storages;

use Core\Services\DatabaseService;

class DatabaseStorage implements ABTestStorageInterface
{

    private DatabaseService $db;

    public function __construct()
    {
        $this->db = DatabaseService::getInstance();
    }

    public function getDesignCounts(int $promotionId): array
    {
        $counts = $this->db->select(
            table: 'ab_tests_counts',
            columns: ['count', 'design_id', 'promotion_id'],
            conditions: [['promotion_id', '=', $promotionId]]
        );

        $designIds = array_column($counts, 'design_id');
        $counts    = array_column($counts, 'count');

        return array_combine($designIds, $counts);
    }

    public function incrementDesignCount(int $promotionId, int $designId): void
    {
        $sql = "INSERT INTO ab_tests_counts (promotion_id, design_id, count) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE count = count + 1";
        $this->db->execute($sql, [$promotionId, $designId]);
    }
}
