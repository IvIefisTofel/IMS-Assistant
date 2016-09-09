<?php

namespace Clients;

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'router' => [
        'routes' => [
            'api-clients' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/clients[/][/:task[/][/:id[/]]]',
                    'constraints' => [
                        'task'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Clients\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
            'forms-clients' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/forms/clients[/:action[/]]',
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Clients\Controller',
                        'controller'    => 'Forms',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Clients\Controller\Index' => Controller\IndexController::class,
            'Clients\Controller\Forms' => Controller\FormsController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'ClientsPlugin' => Controller\Plugin\ClientsPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Clients\Controller\Index' => [
                    GUEST_ROLE,
                ],
                'Clients\Controller\Forms' => [
                    GUEST_ROLE,
                ],
            ],
        ],
    ],

    'view_manager' => [
        'display_not_found_reason'  => $env,
        'display_exceptions'        => $env,
        'doctype'                   => 'HTML5',
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
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