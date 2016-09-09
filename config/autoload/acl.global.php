<?php

return [
    'acl' => [
        'roles' => [
            GUEST_ROLE      => null,
            USER_ROLE       => GUEST_ROLE,
            MODERATOR_ROLE  => USER_ROLE,
            ADMIN_ROLE      => MODERATOR_ROLE,
        ],
        'resources' => [
            'allow' => [
                // -------------------------------- Vendor controllers --------------------------------------------- //
                'DoctrineModule\Controller\Cli' => [
                    GUEST_ROLE,
                ],
                'AssetManager\Controller\Console' => [
                    GUEST_ROLE,
                ],
            ],
        ],
    ],
];
