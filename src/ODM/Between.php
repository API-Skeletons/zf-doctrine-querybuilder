<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ODM;

class Between extends AbstractFilter
{
    public function filter($queryBuilder, $metadata, $option)
    {
        $queryType = 'addAnd';

        if (isset($option['where'])) {
            if ($option['where'] === 'and') {
                $queryType = 'addAnd';
            } elseif ($option['where'] === 'or') {
                $queryType = 'addOr';
            }
        }

        $format = isset($option['format']) ? $option['format'] : null;

        $from = $this->typeCastField($metadata, $option['field'], $option['from'], $format);
        $to   = $this->typeCastField($metadata, $option['field'], $option['to'], $format);

        $queryBuilder->$queryType($queryBuilder->expr()->field($option['field'])->range($from, $to));
    }
}
