<?php

namespace ZF\Doctrine\QueryBuilder\Filter;

interface FilterInterface
{
    public function filter($queryBuilder, $metadata, $option);
}
