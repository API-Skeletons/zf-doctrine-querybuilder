<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\Service;

use ZF\Doctrine\QueryBuilder\Filter\FilterInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;
use Doctrine\ORM\QueryBuilder;

class ORMFilterManager extends AbstractPluginManager
{
    protected $invokableClasses = array();

    public function filter(QueryBuilder $queryBuilder, $metadata, $filters)
    {
        foreach ($filters as $option) {
            if (!isset($option['type']) or !$option['type']) {
                // @codeCoverageIgnoreStart
                throw new Exception\RuntimeException('Array element "type" is required for all filters');
            }
            // @codeCoverageIgnoreEnd

            $filter = $this->get(strtolower($option['type']), [$this]);

            $filter->filter($queryBuilder, $metadata, $option);
        }
    }

    /**
     * @param mixed $filter
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($filter)
    {
        if ($filter instanceof FilterInterface) {
            // we're okay
            return;
        }

        // @codeCoverageIgnoreStart
        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Plugin\PluginInterface',
            (is_object($filter) ? get_class($filter) : gettype($filter)),
            __NAMESPACE__
        ));
        // @codeCoverageIgnoreEnd
    }
}
