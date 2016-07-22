<?php

namespace AuthDoctrine;

$env = (getenv('APP_ENV') == 'development') ? true : false;
return [
    'router' => [
        'routes' => [
            'login' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        '__NAMESPACE__' => 'AuthDoctrine\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
            'logout' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/logout[/]',
                    'defaults' => [
                        '__NAMESPACE__' => 'AuthDoctrine\Controller',
                        'controller'    => 'Index',
                        'action'        => 'logout',
                    ],
                ],
                'may_terminate' => true,
            ],
            'lock-screen' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/lockscreen[/]',
                    'defaults' => [
                        '__NAMESPACE__' => 'AuthDoctrine\Controller',
                        'controller'    => 'Admin',
                        'action'        => 'lockscreen',
                    ],
                ],
                'may_terminate' => true,
            ],
		],
	],

    'controllers' => [
        'invokables' => [
            'AuthDoctrine\Controller\Index' => Controller\IndexController::class,
            'AuthDoctrine\Controller\Admin' => Controller\AdminController::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => $env,
        'display_exceptions'       => $env,
        'template_map' => [
            'auth/index'           => __DIR__ . '/../view/auth-doctrine/index/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view'
        ],
    ],

//    'asset_manager' => [
//        'resolver_configs' => [
//            'collections' => [
//                'css/login-core.css' => [
//                    'css/bootstrap.min.css',
//                    'css/font-awesome.min.css',
//                    'admin/css/plugins/toastr/toastr.css',
//                    'admin/css/animate.css',
//                    'admin/css/inspinia.css',
//                    'admin/css/style.css',
//                ],
//                'js/login-core.js' => [
//                    'js/jquery.min.js',
//                    'js/bootstrap.min.js',
//                    'admin/js/plugins/toastr/toastr.min.js',
//                ],
//            ],
//        ],
//    ],

    'doctrine' => [
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Users\Entity\User',
                'identity_property' => 'userName',
                'credential_property' => 'userPassword',
                'credential_callable' => function(\Users\Entity\User $user, $passwordGiven) {
                    if ($user->getUserPassword() == md5($passwordGiven) && $user->getUserActive() == 1)
                        return true;
                    else
                        return false;
                },
            ],
        ],
    ],
];