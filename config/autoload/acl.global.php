<?php

return [
    'acl' => [
        'roles' => [
            GUEST_ROLE                        => null,
            USER_ROLE                         => GUEST_ROLE,
            MODERATOR_ROLE                    => USER_ROLE,
            ADMIN_ROLE                        => MODERATOR_ROLE,
        ],
        'resources' => [
            'allow' => [
                /**
                 * Authentication doctrine command line
                 */
                'DoctrineModule\Controller\Cli' => [
                    GUEST_ROLE,
                ],
                // -------------------------------- Authentication -------------------------------------------------- //
                'AuthDoctrine\Controller\Index' => [
                    GUEST_ROLE,
                ],
                'AuthDoctrine\Controller\Admin' => [
                    GUEST_ROLE,
                ],
                // -------------------------------- Admin ----------------------------------------------------------- //
                'Admin\Controller\Index' => [
                    MODERATOR_ROLE,
                ],
                // -------------------------------- Client ---------------------------------------------------------- //
                'Application\Controller\Index' => [
                    GUEST_ROLE,
                ],
            ],
        ],
    ],
];
