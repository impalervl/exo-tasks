<?php

declare(strict_types=1);

return [
    //possible values db, cache
    'storage'    => env('AB_TEST_STORAGE', 'db'),

    //possible values storage, random
    'randomizer' => env('AB_TEST_RANDOMIZER', 'storage'),

    // set true to stick current promotion design to user session.
    'ignore_session' => env('AB_TEST_IGNORE_SESSION', true),

    //cookie name for assigned promotion design storage across session
    'cookie_name' => env('AB_TEST_COOKIE', 'ab_test_design'),
];
