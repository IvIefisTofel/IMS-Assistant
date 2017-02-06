<?php

namespace Services;

return [
    'router' => [
        'routes' => [
            'srv-notifications' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/api/services/notifications[/]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Services\Controller',
                        'controller'    => 'Notifications',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Services\Controller\Notifications' => Controller\NotificationsController::class,
        ],
    ],

//    'controller_plugins' => [
//        'invokables' => [
//            'Plugin' => Controller\Plugin\Plugin::class,
//        ],
//    ],

    'acl' => [
        'resources' => [
            'allow' => [
                'Services\Controller\Notifications' => [
                    GUEST_ROLE,
                ],
            ],
        ],
    ],
];