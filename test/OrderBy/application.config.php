<?php

return [
    'modules' => [
        'Zend\Router',
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineMongoODMModule',
        'Db',
        'DbMongo',
        'ZF\Doctrine\QueryBuilder',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/local.php',
        ],
        'module_paths' => [
            __DIR__ . '/../vendor',
            'DbMongo' => __DIR__ . '/module/DbMongo',
            'Db' => __DIR__ . '/module/Db',
            'ZF\Doctrine\QueryBuilder' => __DIR__ . '/../..',
        ],
    ],
];
