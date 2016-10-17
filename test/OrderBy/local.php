<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => [
                    'memory' => true,
                ],
            ],
            'odm_default' => [
                'server' => 'localhost',
                'port' => '27017',
                'user' => '',
                'password' => '',
                'dbname' => 'zf_doctrine_querybuilder_filter_test',
            ],
        ],
    ],
    'zf-doctrine-querybuilder-orderby-orm' => [
        'invokables' => [
            'field' => 'ZF\Doctrine\QueryBuilder\OrderBy\ORM\Field',
        ],
    ],
    'zf-doctrine-querybuilder-orderby-odm' => [
        'invokables' => [
            'field' => 'ZF\Doctrine\QueryBuilder\OrderBy\ODM\Field',
        ],
    ],
];
