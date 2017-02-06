<?php

namespace Files;

return [
    'router' => [
        'routes' => [
            'files' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/files[/:aspect[/:size]][/:versionId[/:fileName]]',
                    'constraints' => [
                        'aspect' => 'w|h',
                        'size' => '[0-9]+',
                        'versionId' => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Files\Controller',
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
            'Files\Controller\Index' => Controller\IndexController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'FilesPlugin' => Controller\Plugin\FilesPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Files\Controller\Index' => [
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