<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder;

use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\ModuleManager;

class Module implements DependencyIndicatorInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function init(ModuleManager $moduleManager)
    {
        $serviceManager  = $moduleManager->getEvent()->getParam('ServiceManager');
        $serviceListener = $serviceManager->get('ServiceListener');

        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderFilterManagerOrm',
            'zf-doctrine-querybuilder-filter-orm',
            'ZF\Doctrine\QueryBuilder\Filter\FilterInterface',
            'getDoctrineQueryBuilderFilterOrmConfig'
        );

        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderFilterManagerOdm',
            'zf-doctrine-querybuilder-filter-odm',
            'ZF\Doctrine\QueryBuilder\Filter\FilterInterface',
            'getDoctrineQueryBuilderFilterOdmConfig'
        );

        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderOrderByManagerOrm',
            'zf-doctrine-querybuilder-orderby-orm',
            'ZF\Doctrine\QueryBuilder\OrderBy\OrderByInterface',
            'getDoctrineQueryBuilderOrderByOrmConfig'
        );
        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderOrderByManagerOdm',
            'zf-doctrine-querybuilder-orderby-odm',
            'ZF\Doctrine\QueryBuilder\OrderBy\OrderByInterface',
            'getDoctrineQueryBuilderOrderByOdmConfig'
        );
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return array('DoctrineModule');
    }
}
