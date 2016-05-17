<?php
namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

class MatchAgainst extends AbstractFilter
{

    public function filter($queryBuilder, $metadata, $option)
    {
        if (isset($option['where'])) {
            if ($option['where'] === 'and') {
                $queryType = 'andWhere';
            } elseif ($option['where'] === 'or') {
                $queryType = 'orWhere';
            }
        }
        
        if (! isset($queryType)) {
            $queryType = 'andWhere';
        }
        
        if (! isset($option['alias'])) {
            $option['alias'] = 'row';
        }
        
        $format = isset($option['format']) ? $option['format'] : null;
        
        $value = $this->typeCastField($metadata, $option['field'], $option['value'], $format);
        
        // $parameter = uniqid('a');
        
        if (! is_array($option['field'])) {
            if (strpos($option['field'], ',') !== false) {
                $option['field'] = explode(',', $option['field']);
            }
        }
        
        if (is_array($option['field'])) {
            $fields = array();
            foreach ($option['field'] as $field) {
                $field = utf8_encode($field);
                $fields[] = "{$option['alias']}.{$field}";
            }
            $option['field'] = implode(',', $fields);
        } else {
            $option['field'] = "{$option['alias']}.{$option['field']}";
        }
        
        $queryBuilder->addSelect("MATCH ({$option['field']}) AGAINST (:searchterm) match_score")
            ->add('where', "MATCH ({$option['field']}) AGAINST (:searchterm) > 0.8")
            ->setParameter('searchterm', $option['value'])
            ->orderBy('match_score', 'desc');
        
    }
}