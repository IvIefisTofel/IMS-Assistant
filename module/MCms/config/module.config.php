<?php

namespace MCms;

return [
    'controller_plugins' => [
        'invokables' => [
            'LitHelperPlugin' => Controller\Plugin\HelperPlugin::class,
        ],
    ],

    'controllers' => [
        'invokables' => [
            'MCms\Controller\Console' => Controller\ConsoleController::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'MCms\Controller\Console' => [
                    GUEST_ROLE,
                ],
            ],
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'compile-mo' => [
                    'options' => [
                        'route'    => 'compile-mo',
                        'defaults' => [
                            'controller' => 'MCms\Controller\Console',
                            'action'     => 'compileMo',
                        ],
                    ],
                ],
            ],
        ],
    ],
];