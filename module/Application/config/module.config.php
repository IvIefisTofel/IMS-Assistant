<?php

namespace Application;

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'api-permissions' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/api/get-permissions',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'permissions',
                    ],
                ],
                'may_terminate' => true,
            ],
            /*'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/app',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],*/
        ],
    ],

    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ],
    ],

    'translator' => [
        'locale' => 'ru_RU',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => './languages',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => Controller\IndexController::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Application\Controller\Index' => [
                    GUEST_ROLE,
                ],
            ],
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => $env,
        'display_exceptions'       => $env,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'        => __DIR__ . '/../view/layout/layout.phtml',
            'error/layout'         => __DIR__ . '/../view/error/layout.phtml',
            'error/404'            => __DIR__ . '/../view/error/404.phtml',
            'error/index'          => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];