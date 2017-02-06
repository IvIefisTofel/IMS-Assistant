<?php
return [
    // Внешние модули
    'DoctrineModule',
    'DoctrineORMModule',
    'AssetManager',
    'TwbBundle',
    // Мои иодули
    'MCms',             // Свои хелперы и пр.
    //'Files',            // Модуль должен быть в начале списка (иначе может возникнуть конфликт роутов)
    //'Navigator',        // Отвечает за навигацию
    'AuthDoctrine',     // Отвечает за авторизацию и ACL (распределение прав доступа)

    // Модули с моделями базы данных
    'Users',
    'Clients',
    'Files',
    'Nomenclature',
    'Orders',
    'Services',

    // Модули, в основном отвечающие за вывод (Admin/Client)
    'Application',
];
