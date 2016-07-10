<?php

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'admin' => [
        'layout' => 'layout/admin',
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                './public'
            ],
        ],
        'filters' => $env ? [] : [
            'application/javascript' => [['filter' => 'JSMin']],
        ],
    ],
];