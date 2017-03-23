<?php

namespace Clients;

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
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'clients' => Controller\Plugin\ClientsPlugin::class,
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