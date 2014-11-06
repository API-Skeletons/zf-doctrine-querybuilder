<?php

namespace ZF\Doctrine\QueryBuilder\Filter\Service;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

class ORMFilterManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'ZF\Doctrine\QueryBuilder\Filter\Service\ORMFilterManager';
}
