<?php

namespace Users;

return [
    'router' => [
        'routes' => [
            'users' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/users[/][/:task[/][/:id[/]]]',
                    'constraints' => [
                        'task'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'    => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
            'users-list' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/users/:task[/]',
                    'constraints' => [
                        'task' => 'get-name-list|getnamelist|getNameList|get-Name-List',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Index',
                        'action'        => 'nameList',
                    ],
                ],
                'may_terminate' => true,
            ],
            'current-user' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/users/:task[/]',
                    'constraints' => [
                        'task' => 'get-identity|getidentity|getIdentity|get-Identity',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Index',
                        'action'        => 'identity',
                    ],
                ],
                'may_terminate' => true,
            ],
            'edit-current-user' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/users/:task[/]',
                    'constraints' => [
                        'task' => 'edit-identity|editidentity|editIdentity|edit-Identity',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Index',
                        'action'        => 'editIdentity',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Users\Controller\Index' => Controller\IndexController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'users' => Controller\Plugin\UsersPlugin::class,
        ],
    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Users\Controller\Index' => [
                    'index'        => SUPERVISOR_ROLE,
                    'nameList'     => USER_ROLE,
                    'identity'     => USER_ROLE,
                    'editIdentity' => USER_ROLE,
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