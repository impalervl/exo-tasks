<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Core\Console\Contracts\CommandInterface;
use Core\Services\DatabaseService;

class CreateTvSeriesTablesCommand implements CommandInterface
{
    private DatabaseService $dbService;

    public function __construct()
    {
        $this->dbService = DatabaseService::getInstance();
    }

    public function handle(array $args): void
    {
        $this->dbService->execute("
            CREATE TABLE IF NOT EXISTS tv_series (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                channel VARCHAR(255) NOT NULL,
                genre VARCHAR(100) NOT NULL,
                CONSTRAINT uc_title_channel UNIQUE (title, channel)
            )
        ");

        echo 'Created TV series table successfully.' . PHP_EOL;

        $this->dbService->execute("
            CREATE TABLE IF NOT EXISTS tv_series_intervals (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_tv_series INT NOT NULL,
                week_day INT NOT NULL,
                show_time TIME NOT NULL,
                FOREIGN KEY (id_tv_series) REFERENCES tv_series(id),
                CONSTRAINT uc_tv_series_interval UNIQUE (week_day, id_tv_series, show_time)
            )
        ");

        echo 'Created TV series interval table successfully.' . PHP_EOL;
    }

    public function getName(): string
    {
        return 'create-tv-series-tables';
    }
}
