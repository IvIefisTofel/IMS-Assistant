<?php

namespace Users;

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'router' => [
        'routes' => [
            'admin-users' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/admin/users[/[:action[/:id]]][[/:page]-page]',
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'page'       => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Admin',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Users\Controller\Admin' => Controller\AdminController::class,
        ],
    ],

    'admin' => [
        'Users\Controller\Admin' => [],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Users\Controller\Admin' => [
                    'edituser' => MODERATOR_ROLE,
                    ADMIN_ROLE,
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

    'navigation' => [
        'default' => [
            [
                'label'     => 'Пользоватли',
                'route'     => 'admin-users',
                'icon'      => 'fa fa-users',
                'resource'	=> 'Users\Controller\Admin',
                'privilege'	=> 'index',
                'order'     => 80,
            ],
        ],
    ],

    'doctrine' => [
        'driver' => [
            strtolower(__NAMESPACE__) . '_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity/',
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