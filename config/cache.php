<?php

declare(strict_types=1);

return [
    'default' => 'redis',
    'redis' => [
        'host' => getenv('REDIS_HOST') ?: 'redis',
        'port' => getenv('REDIS_PORT') ?: 6379,
    ]
];
