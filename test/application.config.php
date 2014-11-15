<?php

return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineMongoODMModule',
        'Db',
        'DbMongo',
        'Zf\Doctrine\QueryBuilder\Filter',
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/local.php',
        ),
        'module_paths' => array(
            __DIR__ . '/../vendor',
            'DbMongo' => __DIR__ . '/module/DbMongo',
            'Db' => __DIR__ . '/module/Db',
            'Zf\Doctrine\QueryBuilder\Filter' => __DIR__ . '/../src',
        ),
    ),
);
