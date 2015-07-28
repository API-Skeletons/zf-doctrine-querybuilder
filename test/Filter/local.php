<?php

date_default_timezone_set('UTC');

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'memory' => true,
                ),
            ),
            'odm_default' => array(
                'server' => 'localhost',
                'port' => '27017',
                'user' => '',
                'password' => '',
                'dbname' => 'zf_doctrine_querybuilder_filter_test',
            ),
        ),
    ),
    'zf-doctrine-querybuilder-filter-orm' => array(
        'invokables' => array(
            'eq' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\Equals',
            'neq' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\NotEquals',
            'lt' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\LessThan',
            'lte' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\LessThanOrEquals',
            'gt' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\GreaterThan',
            'gte' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\GreaterThanOrEquals',
            'isnull' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\IsNull',
            'isnotnull' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\IsNotNull',
            'in' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\In',
            'notin' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\NotIn',
            'between' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\Between',
            'like' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\Like',
            'notlike' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\NotLike',
            'ismemberof' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\IsMemberOf',
            'orx' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\OrX',
            'andx' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\AndX',
            'innerjoin' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\InnerJoin',
        ),
    ),

    'zf-doctrine-querybuilder-filter-odm' => array(
        'invokables' => array(
            'eq' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\Equals',
            'neq' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\NotEquals',
            'lt' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\LessThan',
            'lte' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\LessThanOrEquals',
            'gt' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\GreaterThan',
            'gte' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\GreaterThanOrEquals',
            'isnull' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\IsNull',
            'isnotnull' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\IsNotNull',
            'in' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\In',
            'notin' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\NotIn',
            'between' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\Between',
            'like' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\Like',
            'regex' => 'ZF\Doctrine\QueryBuilder\Filter\ODM\Regex',
        ),
    ),
);
