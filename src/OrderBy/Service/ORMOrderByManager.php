<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\OrderBy\Service;

use RuntimeException;
use ZF\Doctrine\QueryBuilder\OrderBy\OrderByInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;
use Doctrine\ORM\QueryBuilder;

class ORMOrderByManager extends AbstractPluginManager
{
    /**
     * @var string
     */
    protected $instanceOf = OrderByInterface::class;

    public function orderBy(QueryBuilder $queryBuilder, $metadata, $orderBy)
    {
        foreach ($orderBy as $option) {
            if (empty($option['type'])) {
                throw new RuntimeException('Array element "type" is required for all orderby directives');
            }

            $orderByHandler = $this->get(strtolower($option['type']), [$this]);
            $orderByHandler->orderBy($queryBuilder, $metadata, $option);
        }
    }

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param mixed $instance
     * @return void
     * @throws Exception\InvalidServiceException
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            if (! $instance instanceof $this->instanceOf) {
                throw new Exception\InvalidServiceException(sprintf(
                    '%s can only create instances of %s; %s is invalid',
                    get_class($this),
                    $this->instanceOf,
                    is_object($instance) ? get_class($instance) : gettype($instance)
                ));
            }
        }
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @param mixed $instance
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (Exception\InvalidServiceException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
