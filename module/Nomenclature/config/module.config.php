<?php

namespace Nomenclature;

return [
    'router' => [
        'routes' => [
            'api-nomenclature' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/nomenclature[/][/:task[/][/:id[/]]]',
                    'constraints' => [
                        'task'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'    => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Nomenclature\Controller',
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
            'Nomenclature\Controller\Index' => Controller\IndexController::class,
        ],
    ],

    'controller_plugins' => [
        'invokables' => [
            'details' => Controller\Plugin\DetailsPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Nomenclature\Controller\Index' => [
                    USER_ROLE,
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