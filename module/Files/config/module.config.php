<?php

namespace Files;

$env = (getenv('APP_ENV') == 'development') ? true : false;
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
            /*'api-files' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/files[/:action[/:id]]',
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Files\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
            ],*/
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