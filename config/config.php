<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache configuration
    |--------------------------------------------------------------------------
    |
    | The FeedReader and Explorer can cache requests automatically for you to
    | increase performance.
    |
    */
    'cache' => [
        'explorer' => [
            'seconds' => 86400, // One day
        ],
        'reader' => [
            'seconds' => 1800, // 30 minutes
        ],
    ],
];
