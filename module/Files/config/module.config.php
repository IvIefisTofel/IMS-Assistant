<?php

namespace Files;

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'router' => [
        'routes' => [
            'file' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/[:alias]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Files\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'alias' => '[\s\S]*',
                    ],
                ],
            ],
            'admin-files' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/admin/files[/[:action[/:id]]][[/:page]-page]',
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'page'       => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Files\Controller',
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
            'Files\Controller\Index' => Controller\IndexController::class,
            'Files\Controller\Admin' => Controller\AdminController::class,
        ],
    ],

    'admin' => [
        'Files\Controller\Admin' => [],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Files\Controller\Index' => [
                    GUEST_ROLE,
                ],
                'Files\Controller\Admin' => [
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
                'label'     => 'Файлы',
                'route'     => 'admin-files',
                'icon'      => 'fa fa-folder-open',
                'resource'  => 'Files\Controller\Admin',
                'privilege' => 'index',
                'order'     => 60,
            ],
//            [
//                'label'     => 'Файлы',
//                'uri'       => '#',
//                'icon'      => 'fa fa-folder-open',
//                'pages'     => [
//                    [
//                        'label'      => 'Все файлы',
//                        'route'      => 'admin-files',
//                        'action'     => 'index',
//                        'icon'       => 'fa fa-files-o',
//                        'resource'   => 'Files\Controller\Admin',
//                        'privilege'  => 'index',
//                    ],
//                    [
//                        'label'      => 'Изображения',
//                        'route'      => 'admin-files',
//                        'action'     => 'images',
//                        'icon'       => 'fa fa-picture-o',
//                        'resource'   => 'Files\Controller\Admin',
//                        'privilege'  => 'index',
//                    ],
//                ],
//                'order'      => 60,
//            ],
//        ],
        ],
    ],
];