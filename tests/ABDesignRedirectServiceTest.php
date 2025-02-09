<?php

declare(strict_types=1);

namespace Tests;

use App\Console\Commands\CreateABTestStorageTableCommand;
use App\Services\ABTest\ABTestService;
use App\Services\ABTest\Storages\CacheStorage;
use Core\Helpers\Config;
use Core\Services\CacheService;
use Exads\ABTestData;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Common\TestCase;


class ABDesignRedirectServiceTest extends TestCase
{
    protected static bool $migrationRan = false;

    protected const int TOTAL_ASSIGNMENTS = 100;

    #[DataProvider('splitLogicDataProvider')]
    public function testSplitLogic(string $driver, int $promotionId, array $mockedDesigns): void
    {
        $abTestMock = Mockery::mock(ABTestData::class);
        $abTestMock->shouldReceive('getAllDesigns')->andReturn($mockedDesigns);
        Config::set('ab-test.storage', $driver);

        $service = new ABTestService($abTestMock);

        $assignments = [];

        for ($i = 0; $i <= self::TOTAL_ASSIGNMENTS; $i++) {
            $design                   = $service->getAssignedDesign($promotionId);
            $assignments[$design->id] = ($assignments[$design->id] ?? 0) + 1;
        }

        $realAssignments = match ($driver) {
            'db'    => $this->fromDb($promotionId),
            'cache' => $this->fromCache($promotionId),
        };

        $this->assertAssignments($mockedDesigns, $realAssignments, $assignments);
    }

    private function fromDb(int $promotionId): array
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

    private function fromCache(int $promotionId): array
    {
        return (new CacheStorage())->getDesignCounts($promotionId);
    }

    private function assertAssignments(array $mockedDesigns, array $realAssignments, array $assignments): void
    {
        foreach ($mockedDesigns as $design) {
            $expectedCount = $this->calculateExpectedCount($design['splitPercent']);
            $actualCount = $assignments[$design['designId']] ?? 0;

            $tolerance = (7 / 100) * $expectedCount;

            $this->assertGreaterThanOrEqual($expectedCount - $tolerance, $actualCount);
            $this->assertLessThanOrEqual($expectedCount + $tolerance, $actualCount);
            $this->assertEquals($actualCount, $realAssignments[$design['designId']]);
        }
    }

    private function calculateExpectedCount(int $splitPercent): int
    {
        return (int) (($splitPercent / 100) * self::TOTAL_ASSIGNMENTS);
    }

    public static function splitLogicDataProvider(): array
    {
        $percentages = [
            'equal-split' => [
                'promotion_id'  => 1,
                'mockedDesigns' => [
                    ['designId' => 101, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 50],
                    ['designId' => 102, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 50],
                ],
            ],
            'unequal-split' => [
                'promotion_id'  => 2,
                'mockedDesigns' => [
                    ['designId' => 201, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 30],
                    ['designId' => 202, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 70],
                ],
            ],
            'three-way-split' => [
                'promotion_id'  => 3,
                'mockedDesigns' => [
                    ['designId' => 301, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 20],
                    ['designId' => 302, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 50],
                    ['designId' => 303, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 30],
                ],
            ],
            'four-way-split' => [
                'promotion_id'  => 4,
                'mockedDesigns' => [
                    ['designId' => 301, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 20],
                    ['designId' => 302, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 50],
                    ['designId' => 303, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 15],
                    ['designId' => 304, 'designName' => 'Design-' . rand(1000, 9999), 'splitPercent' => 15],
                ],
            ],
        ];

        $data  = [];
        $count = count($percentages);

        foreach ($percentages as $value) {
            $data[] = [
                'driver'        => 'db',
                'promotionId'   => $value['promotion_id'],
                'mockedDesigns' => $value['mockedDesigns'],
            ];

            $data[] = [
                'driver'        => 'cache',
                'promotionId'   => $value['promotion_id'] + $count,
                'mockedDesigns' => $value['mockedDesigns'],
            ];
        }

        return $data;
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::$migrationRan) {
            $migration = new CreateABTestStorageTableCommand();
            $migration->handle([]);
            self::$migrationRan = true;
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        CacheService::getInstance()->clear();
    }
}
