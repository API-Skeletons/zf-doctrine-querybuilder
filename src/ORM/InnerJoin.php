<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

use Exception;

class InnerJoin extends AbstractFilter
{
    public function filter($queryBuilder, $metadata, $option)
    {
        if (! isset($option['field']) || ! $option['field']) {
            // @codeCoverageIgnoreStart
            throw new Exception('Field must be specified for inner join');
        }
            // @codeCoverageIgnoreEnd

        if (! isset($option['alias']) || ! $option['alias']) {
            // @codeCoverageIgnoreStart
            throw new Exception('Alias must be specified for inner join');
        }
            // @codeCoverageIgnoreEnd

        if (! isset($option['parentAlias']) || ! $option['parentAlias']) {
            $option['parentAlias'] = 'row';
        }

        if (! isset($option['conditionType']) && isset($option['condition'])) {
            throw new Exception('A conditionType must be specified for a condition');
        }

        if (! isset($option['condition']) && isset($option['conditionType'])) {
            throw new Exception('A condition must be specified for a conditionType');
        }

        if (! isset($option['conditionType'])) {
            $option['conditionType'] = null;
        }

        if (! isset($option['condition'])) {
            $option['condition'] = null;
        }

        if (! isset($option['indexBy'])) {
            $option['indexBy'] = null;
        }

        $queryBuilder->innerJoin(
            $option['parentAlias'] . '.' . $option['field'],
            $option['alias'],
            $option['conditionType'],
            $option['condition'],
            $option['indexBy']
        );
    }
}
