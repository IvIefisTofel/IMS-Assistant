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
            'Application\Controller\Index' => \Application\Controller\IndexController::class
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
    
    'asset_manager' => [
        'resolver_configs' => [
            'aliases' => $env ? [
                'bower/' => __DIR__ . '/../../../bower_components/',
            ] : [],
            /*'collections' => $env ? [] : [
                'css/core.css' => [
                    'css/bootstrap.min.css',
                    'css/font-awesome.min.css',
                    'admin/css/plugins/toastr/toastr.css',
                    'admin/css/plugins/gritter/jquery.gritter.css',
                    'admin/css/plugins/iCheck/custom.css',
                    'admin/css/animate.css',
                    'admin/css/inspinia.css',
                    'admin/css/style.css',
                ],
                'js/core.js' => [
                    'js/jquery.min.js',
                    'js/plugins/jQueryUI/jquery-ui.min.js',
                    'js/fixConflictUI.js',
                    'js/bootstrap.min.js',
                    'admin/js/plugins/metisMenu/jquery.metisMenu.js',
                    'admin/js/plugins/slimscroll/jquery.slimscroll.min.js',
                    'admin/js/plugins/flot/jquery.flot.js',
                    'admin/js/plugins/flot/jquery.flot.tooltip.min.js',
                    'admin/js/plugins/flot/jquery.flot.spline.js',
                    'admin/js/plugins/flot/jquery.flot.resize.js',
                    'admin/js/plugins/flot/jquery.flot.pie.js',
                    'admin/js/plugins/peity/jquery.peity.min.js',
                    'admin/js/plugins/gritter/jquery.gritter.min.js',
                    'admin/js/plugins/sparkline/jquery.sparkline.min.js',
                    'admin/js/plugins/toastr/toastr.min.js',
                    'admin/js/plugins/iCheck/icheck.min.js',
                    'admin/js/plugins/pace/pace.min.js',
                    'admin/js/inspinia.js',
                ],
            ],
            'paths' => [
                __DIR__ . '/../public',
            ],*/
        ],
        /*'filters' => $env ? [] : [
            'admin/css/plugins/toastr/toastr.css'           => [['filter' => 'CssMinFilter']],
            'admin/css/plugins/gritter/jquery.gritter.css'  => [['filter' => 'CssMinFilter']],
            'admin/css/plugins/iCheck/custom.css'           => [['filter' => 'CssMinFilter']],
            'admin/css/animate.css'                         => [['filter' => 'CssMinFilter']],
            'admin/css/inspinia.css'                        => [['filter' => 'CssMinFilter']],
            'admin/css/style.css'                           => [['filter' => 'CssMinFilter']],
        ],
        'caching' => $env ? [] : [
            'css/core.css' => [
                'cache'     => 'FilesystemCache',
                'options' => [
                    'dir' => './public/css/core',
                ],
            ],
            'js/core.js' => [
                'cache'     => 'FilesystemCache',
                'options' => [
                    'dir' => './public/js/core',
                ],
            ],
        ],*/
    ],
];