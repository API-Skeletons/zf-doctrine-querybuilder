<?php

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

class AndX extends AbstractFilter
{
    public function filter($queryBuilder, $metadata, $option)
    {
        if (isset($option['where'])) {
            if ($option['where'] == 'and') {
                $queryType = 'andWhere';
            } elseif ($option['where'] == 'or') {
                $queryType = 'orWhere';
            }
        }

        if (!isset($queryType)) {
            $queryType = 'andWhere';
        }

        $andX = $queryBuilder->expr()->andX();
        $em = $queryBuilder->getEntityManager();
        $qb = $em->createQueryBuilder();

        foreach ($option['conditions'] as $condition) {
            $filter = $this->getFilterManager()->get(strtolower($condition['type']), array($this->getFilterManager()));
            $filter->filter($qb, $metadata, $condition);
        }

        $dqlParts = $qb->getDqlParts();
        $andX->addMultiple($dqlParts['where']->getParts());
        $queryBuilder->setParameters($qb->getParameters());

        $queryBuilder->$queryType($andX);
    }
}
