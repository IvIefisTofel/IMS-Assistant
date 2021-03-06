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
                        'task'          => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'            => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'MCms\Controller',
                        'controller'    => 'Errors',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
            'events' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/events[/][/:offset[/]]',
                    'constraints' => [
                        'offset'        => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'MCms\Controller',
                        'controller'    => 'Events',
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
            'MCms\Controller\Errors'   => Controller\ErrorsController::class,
            'MCms\Controller\Events'  => Controller\EventsController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'cmsHelper' => Controller\Plugin\HelperPlugin::class,
            'errors' => Controller\Plugin\ErrorsPlugin::class,
            'events' => Controller\Plugin\EventsPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'MCms\Controller\Console' => [
                    GUEST_ROLE,
                ],
                'MCms\Controller\Errors' => [
                    ADMIN_ROLE,
                ],
                'MCms\Controller\Events' => [
                    INSPECTOR_ROLE,
                ]
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