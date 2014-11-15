<?php

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

use Exception;

class InnerJoin extends AbstractFilter
{
    public function filter($queryBuilder, $metadata, $option)
    {
        if (!isset($option['field']) or !$option['field']) {
            // @codeCoverageIgnoreStart
            throw new Exception('Field must be specified for inner join');
        }
            // @codeCoverageIgnoreEnd

        if (!isset($option['alias']) or !$option['alias']) {
            // @codeCoverageIgnoreStart
            throw new Exception('Alias must be specified for inner join');
        }
            // @codeCoverageIgnoreEnd

        if (!isset($option['parentAlias']) or !$option['parentAlias']) {
            $option['parentAlias'] = 'row';
        }

        if (!isset($option['conditionType']) and isset($option['condition'])) {
            throw new Exception('A conditionType must be specified for a condition');
        }

        if (!isset($option['condition']) and isset($option['conditionType'])) {
            throw new Exception('A condition must be specified for a conditionType');
        }

        if (!isset($option['conditionType'])) {
            $option['conditionType'] = null;
        }

        if (!isset($option['condition'])) {
            $option['condition'] = null;
        }

        if (!isset($option['indexBy'])) {
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
