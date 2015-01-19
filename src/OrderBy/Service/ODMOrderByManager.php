<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\OrderBy\Service;

use ZF\Doctrine\QueryBuilder\OrderBy\OrderByInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;
use Doctrine\ODM\MongoDB\Query\Builder;

class ODMOrderByManager extends AbstractPluginManager
{
    protected $invokableClasses = array();

    public function orderBy(Builder $queryBuilder, $metadata, $orderBy)
    {
        foreach ($orderBy as $option) {
            if (! isset($option['type']) or ! $option['type']) {
                // @codeCoverageIgnoreStart
                throw new Exception\RuntimeException('Array element "type" is required for all orderby directives');
            }
            // @codeCoverageIgnoreEnd

            $orderByHandler = $this->get(strtolower($option['type']), array($this));

            $orderByHandler->orderBy($queryBuilder, $metadata, $option);
        }
    }

    /**
     * @param mixed $orderBy
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($orderBy)
    {
        if ($orderBy instanceof OrderByInterface) {
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
