<?php

namespace AuthDoctrine;

use Users\Entity\Users;

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
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'AuthDoctrine\Controller\Index' => [
                    GUEST_ROLE,
                ],
            ],
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
                'identity_class' => 'Users\Entity\Users',
                'identity_property' => 'name',
                'rules_property' => 'currentRole',
                'credential_property' => 'password',
                'credential_callable' => function(Users $user, $passwordGiven) {
                    if ($user->getPassword() == md5($passwordGiven) && $user->getActive()) {
                        $user->setCurrentRole($user->getRoleID());
                    } else {
                        $user->setCurrentRole(USER_ROLE);
                    }
                    return $user->getActive();
                },
            ],
        ],
    ],
    'doctrine_factories' => array(
        'authenticationadapter' => 'AuthDoctrine\Authentication\Service\AdapterService',
        'authenticationstorage' => 'AuthDoctrine\Authentication\Service\StorageService',
    ),
];