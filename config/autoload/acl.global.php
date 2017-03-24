<?php

return [
    'acl' => [
        'roles' => [
            GUEST_ROLE          => null,
            USER_ROLE           => GUEST_ROLE,
            TECHNOLOGIST_ROLE   => USER_ROLE,
            CONSTRUCTOR_ROLE    => TECHNOLOGIST_ROLE,
            SUPERVISOR_ROLE     => CONSTRUCTOR_ROLE,
            INSPECTOR_ROLE      => SUPERVISOR_ROLE,
            ADMIN_ROLE          => INSPECTOR_ROLE,
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
