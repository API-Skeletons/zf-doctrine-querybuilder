<?php

namespace Db;

return [
    'zf-doctrine-querybuilder-filter-orm' => [
        'invokables' => [
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
            'orx' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\OrX',
            'andx' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\AndX',
        ],
    ],

    'zf-doctrine-querybuilder-filter-odm' => [
        'invokables' => [
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
        ],
    ],

    'doctrine' => [
        'driver' => [
           'db_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'paths' => [__DIR__ . '/xml'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'db_driver',
                ],
            ],
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'json_exceptions' => [
            'display' => true,
            'ajax_only' => true,
            'show_trace' => true,
        ],
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
