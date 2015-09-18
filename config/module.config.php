<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

return array(
    'service_manager' => array(
        'factories' => array(
            'ZfDoctrineQueryBuilderFilterManagerOrm' => 'ZF\Doctrine\QueryBuilder\Filter\Service\ORMFilterManagerFactory',
            'ZfDoctrineQueryBuilderFilterManagerOdm' => 'ZF\Doctrine\QueryBuilder\Filter\Service\ODMFilterManagerFactory',
            'ZfDoctrineQueryBuilderOrderByManagerOrm' => 'ZF\Doctrine\QueryBuilder\OrderBy\Service\ORMOrderByManagerFactory',
            'ZfDoctrineQueryBuilderOrderByManagerOdm' => 'ZF\Doctrine\QueryBuilder\OrderBy\Service\ODMOrderByManagerFactory',
        ),
    ),
);
