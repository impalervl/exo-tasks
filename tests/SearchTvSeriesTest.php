<?php

declare(strict_types=1);

namespace Tests;

use App\Console\Commands\CreateTvSeriesTablesCommand;
use App\Enums\WeekEnum;
use DateTime;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Common\TestCase;

class SearchTvSeriesTest extends TestCase
{
    protected static bool $migrationRan = false;

    #[DataProvider('tvSeriesDataProvider')]
    public function testTvSeriesSearch(
        array $tvSeriesData,
        array $payload,
        array $expectedResult,
        bool $isException = false
    ): void {

        $this->seedData($tvSeriesData);

        try {
            $response = $this->get('/tv-series', $payload);
        } catch (Exception $e) {
            if ($isException) {
                $this->assertTrue(true);
                return;
            }
            $this->fail();
        }

        $result = json_decode($response->getContent(), true);

        if (empty($expectedResult)) {
            $this->assertEmpty($result);
        } else {
            $this->assertNotEmpty($result);
            $this->assertEquals($expectedResult['title'], $result[0]['title']);
            $this->assertEquals($expectedResult['channel'], $result[0]['channel']);
            $this->assertEquals($expectedResult['genre'], $result[0]['genre']);
            $this->assertEquals($expectedResult['week_day'], $result[0]['week_day']);
            $this->assertEquals($expectedResult['show_time'], $result[0]['show_time']);
        }
    }

    public static function tvSeriesDataProvider(): array
    {
        $date                = new DateTime();
        $currentExpectedDay  = (int) $date->format('N');
        $day                 = WeekEnum::tryFrom( $currentExpectedDay);

        return [
            'only_title' => [
                'tvSeriesData' => ['id' => 1, 'title' => 'Test 2', 'channel' => 'Test Channel 5', 'genre' => 'Drama', 'week_day' => $currentExpectedDay, 'show_time' => '23:59:59'],
                'payload' => ['show_title' => 'Test'],
                'expectedResult' => [
                    'title' => 'Test 2',
                    'channel' => 'Test Channel 5',
                    'genre' => 'Drama',
                    'week_day' => $day->name,
                    'show_time' => '23:59:59'
                ],
            ],
            'no_params' => [
                'tvSeriesData' => ['id' => 9, 'title' => 'Morning Show', 'channel' => 'Morning Channel', 'genre' => 'Lifestyle', 'week_day' => $currentExpectedDay, 'show_time' => '23:59:58'],
                'payload' => ['show_title' => 'Morning'], // No parameters provided
                'expectedResult' => [
                    'title'     => 'Morning Show',
                    'channel'   => 'Morning Channel',
                    'genre'     => 'Lifestyle',
                    'week_day'  =>  $day->name,
                    'show_time' => '23:59:58'
                ],
            ],
            'valid_1' => [
                'tvSeriesData' => ['id' => 5, 'title' => 'Test', 'channel' => 'Test Channel 2', 'genre' => 'Comedy', 'week_day' => 1, 'show_time' => '20:00:00'],
                'payload' => ['air_time' => '2031-02-10T19:00:00', 'show_title' => 'Test'],
                'expectedResult' => [
                    'title' => 'Test',
                    'channel' => 'Test Channel 2',
                    'genre' => 'Comedy',
                    'week_day' => 'MONDAY',
                    'show_time' => '20:00:00'
                ]
            ],
            'valid_2' => [
                'tvSeriesData' => ['id' => 6, 'title' => 'Another Test', 'channel' => 'Test Channel 5', 'genre' => 'Drama', 'week_day' => 3, 'show_time' => '21:00:00'],
                'payload' => ['air_time' => '2031-02-12T20:00:00', 'show_title' => 'Another Test'],
                'expectedResult' => [
                    'title' => 'Another Test',
                    'channel' => 'Test Channel 5',
                    'genre' => 'Drama',
                    'week_day' => 'WEDNESDAY',
                    'show_time' => '21:00:00'
                ]
            ],
            'invalid_title' => [
                'tvSeriesData' => ['id' => 2, 'title' => 'Test', 'channel' => 'Test Channel 4', 'genre' => 'Comedy', 'week_day' => 1, 'show_time' => '20:00:00'],
                'payload' => ['air_time' => '2031-02-10T19:00:00', 'show_title' => 'Nonexistent Show'],
                'expectedResult' => []
            ],
            'invalid_date_format' => [
                'tvSeriesData' => ['id' => 7, 'title' => 'Invalid Air Time Test', 'channel' => 'Test Channel 6', 'genre' => 'Thriller', 'week_day' => 2, 'show_time' => '22:00:00'],
                'payload' => ['air_time' => 'invalid-date', 'show_title' => 'Invalid Air Time Test'],
                'expectedResult' => [],
                'isException' => true,
            ],
            'multiple_1' => [
                'tvSeriesData' => ['id' => 8, 'title' => 'Multiple Test 1', 'channel' => 'Test Channel 7', 'genre' => 'Action', 'week_day' => 5, 'show_time' => '18:00:00'],
                'payload' => ['air_time' => '2031-02-14T17:00:00', 'show_title' => 'Multiple Test'],
                'expectedResult' => [
                    'title' => 'Multiple Test 1',
                    'channel' => 'Test Channel 7',
                    'genre' => 'Action',
                    'week_day' => 'FRIDAY',
                    'show_time' => '18:00:00'
                ]
            ],
            'multiple_2' => [
                'tvSeriesData' => ['id' => 3, 'title' => 'Multiple Test 2', 'channel' => 'Test Channel 8', 'genre' => 'Sci-Fi', 'week_day' => 5, 'show_time' => '19:00:00'],
                'payload' => ['air_time' => '2031-02-14T17:00:00', 'show_title' => 'Multiple Test'],
                'expectedResult' => [
                    'title' => 'Multiple Test 2',
                    'channel' => 'Test Channel 8',
                    'genre' => 'Sci-Fi',
                    'week_day' => 'FRIDAY',
                    'show_time' => '19:00:00'
                ]
            ],
            'no_matching_record' => [
                'tvSeriesData' => ['id' => 10, 'title' => 'Late Show', 'channel' => 'Late Night Channel', 'genre' => 'Comedy', 'week_day' => 7, 'show_time' => '23:00:00'],
                'payload' => ['air_time' => '2031-02-15T00:00:00', 'show_title' => 'Late Show'],
                'expectedResult' => []
            ],
            'wrong_day_of_the_week' => [
                'tvSeriesData' => ['id' => 11, 'title' => 'Weekend Special', 'channel' => 'Weekend Channel', 'genre' => 'Talk Show', 'week_day' => 6, 'show_time' => '14:00:00'],
                'payload' => ['air_time' => '2031-02-14T10:00:00', 'show_title' => 'Weekend Special'],
                'expectedResult' => []
            ],
        ];
    }

    protected function seedData(array $data): void
    {
        $this->db->insert('tv_series',
            [
                [
                    'id'      => $data['id'],
                    'title'   => $data['title'],
                    'channel' => $data['channel'],
                    'genre'   => $data['genre'],
                ]
            ]
        );
        $this->db->insert(
            'tv_series_intervals',
            [
                [
                    'id_tv_series' => $data['id'],
                    'week_day'     => $data['week_day'],
                    'show_time'    => $data['show_time'],
                ],
            ]
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::$migrationRan) {
            $migration = new CreateTvSeriesTablesCommand();
            $migration->handle([]);
            self::$migrationRan = true;
        }
    }
}
