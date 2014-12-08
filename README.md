Doctrine QueryBuilder Filters
==============================

[![Build status](https://api.travis-ci.org/zfcampus/zf-doctrine-querybuilder-filter.svg)](http://travis-ci.org/zfcampus/zf-doctrine-querybuilder-filter) 
This library provides query builder filters from array parameters.  This library was designed to apply filters from an HTTP request to give an API a fluent filter dialect.


Installation
------------

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
$ php composer.phar require zfcampus/zf-doctrine-querybuilder-filter:dev-master
```
Add ```ZF\Doctrine\QueryBuilder\Filter``` to your application.config.php list of modules.


Configuring the Module
----------------------
Copy ```config/zf-doctrine-querybuilder-filter.global.php.dist``` to ```config/autoload/zf-doctrine-querybuilder-filter.global.php``` and edit the list of invokables for orm and odm to those you want enabled by default.

The filters you enable by default will be available to anyone with access to a route which uses the filters.

Use
---
```php
$filters = array(
    array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistOne'),
);


$serviceManager = $this->getApplication()->getServiceManager();
$filterManager = $serviceManager->get('ZfDoctrineQueryBuilderFilterManagerOrm');
$objectManager = $serviceManager->get('doctrine.entitymanager.orm_default');
$queryBuilder = $objectManager->createQueryBuilder();
$queryBuilder->select('row')
    ->from($entity, 'row')
;
$filterManager->filter($queryBuilder, $objectManager->getMetadataFactory()->getAllMetadata()[0], $filters);
```

The $filters array is intened to come from the request.



Filters
--------------------

Filters are not simple key=value pairs.  Filters are a key-less array of query
definitions.  Each query definition is an array and the array values vary for each query type.

Each query definition requires at a minimum a 'type' and a 'field'.  Each query may also specify a 'where' which can be either 'and' or 'or'.  Embedded logic such as and(x or y) is supported through AndX and OrX query types.

Building HTTP GET query: 

PHP Example
```php
    echo http_build_query(
        array(
            'query' => array(
                array('field' =>'cycle', 'where' => 'and', 'type'=>'between', 'from' => 1, 'to'=>100),
                array('field'=>'cycle', 'where' => 'and', 'type' => 'decimation', 'value' => 10)
            ),
            'orderBy' => array('columnOne' => 'ASC', 'columnTwo' => 'DESC')
        )
    );
```

Javascript Example
```js
$(function() {
    $.ajax({
        url: "http://localhost:8081/api/db/entity/user_data",
        type: "GET",
        data: {
            'query': [
            {
                'field': 'cycle',
                'where': 'or',
                'type': 'between',
                'from': '1',
                'to': '100'
            },
            {
                'field': 'cycle',
                'where': 'or',
                'type': 'gte',
                'value': '1000'
            }
        ]
        },
        dataType: "json"
    });
});
```

Querying Relations
---------------------
It is possible to query collections by relations - just supply the relation name as `fieldName` and
identifier as `value`.

1. Using an RPC created by this module for each collection on each resource: /resource/id/childresource/child_id

2. Assuming we have defined 2 entities, `User` and `UserGroup`...

````php
/**
 * @Entity
 */
class User {
    /**
     * @ManyToOne(targetEntity="UserGroup")
     * @var UserGroup
     */
    protected $group;
}
````

````php
/**
 * @Entity
 */
class UserGroup {}
````

... we can find all users that belong to UserGroup id #1 with the following query:

````php
    $url = 'http://localhost:8081/api/user';
    $query = http_build_query(array(
        array('type' => 'eq', 'field' => 'group', 'value' => '1')
    ));
````


Format of Date Fields
---------------------

When a date field is involved in a query you may specify the format of the date
using PHP date formatting options.  The default date format is ```Y-m-d H:i:s```
If you have a date field which is just Y-m-d then add the format to the query.

```php
    'format' => 'Y-m-d',
    'value' => '2014-02-04',
```


Joining Entities and Aliasing Queries 
-------------------------------------

There is an available ORM Query Type for Inner Join so for every query type there is an optional ```alias```.
The default alias is 'row' and refers to the entity at the heart of the Rest resource.  

This example joins the report field through the inner join already defined on the row entity then filters
for r.id = 2

```php
    array('type' => 'innerjoin', 'field' => 'report', 'alias' => 'r'),
    array('type' => 'eq', 'alias' => 'r', 'field' => 'id', 'value' => '2')
```

You can inner join tables from an inner join using parentAlias

```php
    array('type' => 'innerjoin', 'parentAlias' => 'r', 'field' => 'owner', 'alias' => 'o'),
```

The Inner Join and On Query Type is not enabled by default.  
To enable it add this to your configuration (e.g. ```config/autoload/global.php```).  There is also an optional On filter used to join two columns (e.g. row.id = other.id)

```php
    'zf-orm-collection-filter' => array(
        'invokables' => array(
            'innerjoin' => 'ZF\Apigility\Doctrine\Server\Collection\Filter\ORM\InnerJoin',
            'on' => 'ZF\Apigility\Doctrine\Server\Collection\Filter\ORM\On',
        ),
    ),
```

To disable any filters do the same but set the value to null

```php
    'zf-orm-collection-filter' => array(
        'invokables' => array(
            'notlike' => null,
        ),
    ),
```

Available Query Types
---------------------

ORM and ODM

Equals

```php
    array('type' => 'eq', 'field' => 'fieldName', 'value' => 'matchValue')
```

Not Equals

```php
    array('type' => 'neq', 'field' => 'fieldName', 'value' => 'matchValue')
```

Less Than

```php
    array('type' => 'lt', 'field' => 'fieldName', 'value' => 'matchValue')
```

Less Than or Equals

```php
    array('type' => 'lte', 'field' => 'fieldName', 'value' => 'matchValue')
```

Greater Than

```php
    array('type' => 'gt', 'field' => 'fieldName', 'value' => 'matchValue')
```

Greater Than or Equals

```php
    array('type' => 'gte', 'field' => 'fieldName', 'value' => 'matchValue')
```

Is Null

```php
    array('type' => 'isnull', 'field' => 'fieldName')
```

Is Not Null

```php
    array('type' => 'isnotnull', 'field' => 'fieldName')
```

Dates in the In and NotIn filters are not handled as dates.
It is recommended you use multiple Equals statements instead of these
filters.

In

```php
    array('type' => 'in', 'field' => 'fieldName', 'values' => array(1, 2, 3))
```

NotIn

```php
    array('type' => 'notin', 'field' => 'fieldName', 'values' => array(1, 2, 3))
```

Between

```php
    array('type' => 'between', 'field' => 'fieldName', 'from' => 'startValue', 'to' => 'endValue')
````

Like (% is used as a wildcard)

```php
    array('type' => 'like', 'field' => 'fieldName', 'value' => 'like%search')
```


ORM Only
--------

AndX 

In AndX queries the ```conditions``` is an array of query types for any of those described
here.  The join will always be ```and``` so the ```where``` parameter inside of conditions is
ignored.  The ```where``` parameter on the AndX query type is not ignored.

```php
array(
    'type' => 'andx',
    'conditions' => array(
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistOne'),
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistTwo'),
    ),
    'where' => 'and'
)
```

OrX 

In OrX queries the ```conditions``` is an array of query types for any of those described
here.  The join will always be ```or``` so the ```where``` parameter inside of conditions is
ignored.  The ```where``` parameter on the OrX query type is not ignored.

```php
array(
    'type' => 'orx',
    'conditions' => array(
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistOne'),
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistTwo'),
    ),
    'where' => 'and'
)
```


ODM Only
--------

Regex

```php
    array('type' => 'regex', 'field' => 'fieldName', 'value' => '/.*search.*/i')
```
