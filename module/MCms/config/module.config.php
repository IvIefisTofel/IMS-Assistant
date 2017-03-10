<?php

namespace MCms;

return [
    'router' => [
        'routes' => [
            'errors' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/errors[/][/:task[/][/:id[/]]]',
                    'constraints' => [
                        'task'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'MCms\Controller',
                        'controller'    => 'Error',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'MCms\Controller\Console' => Controller\ConsoleController::class,
            'MCms\Controller\Error' => Controller\ErrorController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'LitHelperPlugin' => Controller\Plugin\HelperPlugin::class,
            'ErrorsPlugin' => Controller\Plugin\ErrorsPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'MCms\Controller\Console' => [
                    GUEST_ROLE,
                ],
                'MCms\Controller\Error' => [
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

    'doctrine' => [
        'driver' => [
            strtolower(__NAMESPACE__) . '_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],

            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ .'\Entity' => strtolower(__NAMESPACE__) . '_entities',
                ],
            ],
        ],
    ],
];