<?php

namespace Orders;

return [
    'router' => [
        'routes' => [
            'api-orders' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/orders[/][/:task[/][/:id[/]]]',
                    'constraints' => [
                        'task'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Orders\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Orders\Controller\Index' => Controller\IndexController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'OrdersPlugin' => Controller\Plugin\OrdersPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Orders\Controller\Index' => [
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