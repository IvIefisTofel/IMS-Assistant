<?php

namespace Fields;

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'router' => [
        'routes' => [
            'forms' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/forms[/]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Fields\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],

            'admin-forms' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/admin/forms[/[:action[/[:alias]]]]',
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'alias'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Fields\Controller',
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
            'Fields\Controller\Index' => Controller\IndexController::class,
            'Fields\Controller\Admin' => Controller\AdminController::class,
        ],
    ],

    'admin' => [
        'Fields\Controller\Admin' => [],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Fields\Controller\Index' => [
                    GUEST_ROLE,
                ],
                'Fields\Controller\Admin' => [
                    MODERATOR_ROLE,
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
                'label'     => 'Формы',
                'route'     => 'admin-forms',
                'icon'      => 'fa fa-list-alt',
                'resource'	=> 'Fields\Controller\Admin',
                'privilege'	=> 'index',
                'order'     => 50,
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