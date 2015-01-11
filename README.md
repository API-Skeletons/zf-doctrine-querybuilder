ZF Campus Doctrine QueryBuilder
===============================

[![Build status](https://api.travis-ci.org/zfcampus/zf-doctrine-querybuilder.svg)](http://travis-ci.org/zfcampus/zf-doctrine-querybuilder)

This library provides query builder directives from array parameters.  This library was designed to apply filters from an HTTP request to give an API a fluent filter dialect.


Philosophy
----------

Given developers identified A and B:  A == B with respect to ability.

The Doctrine entity to share contains
```
id integer,
name string,
startAt datetime,
endAt datetime,
```

Developer A or B writes the API.  The resource is a single Doctrine Entity and the data is queried using a Doctrine QueryBuilder ```$objectManager->createQueryBuilder()```  This module gives the other developer the same filtering and sorting ability to the Doctrine query builder, but accessed through request parameters, as the API author.  For instance, ```between(startAt, endAt); ``` and ```name like ('%rson')``` are not common API filters for hand rolled APIs and perhaps without this module the API author would choose not to implement it for their reason(s).  With the help of this module the API developer can implement complex queryability to resources without complicated effort thereby maintaining A == B.


Installation
------------

Installation of this module uses composer. For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).

``` console
$ php composer.phar require zfcampus/zf-doctrine-querybuilder ~2.0
```

Once installed, add `ZF\Doctrine\QueryBuilder` to your list of modules inside
`config/application.config.php`.


Configuring the Module
----------------------

Copy `config/zf-doctrine-querybuilder.global.php.dist` to `config/autoload/zf-doctrine-querybuilder.global.php` and edit the list of invokables for orm and odm to those you want enabled by default.


Use
---

See also [docs/apigility.example.php](https://github.com/zfcampus/zf-doctrine-querybuilder/blob/master/docs/apigility.example.php)

Configuration example
```php
    'zf-doctrine-querybuilder-orderby-orm' => array(
        'invokables' => array(
            'field' => 'ZF\Doctrine\QueryBuilder\OrderBy\ORM\Field',
        ),
    ),
    'zf-doctrine-querybuilder-filter-orm' => array(
        'invokables' => array(
            'eq' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\Equals',
        ),
    ),
```

Request example
```php
$_GET = array(
    'filters' => array(
        array(
            'type' => 'eq',
            'field' => 'name',
            'value' => 'Tom',
        ),
    ),
    'orderBy' => array(
        array(
            'type' => 'field,
            'field' => 'startAt',
            'direction' => 'desc',
        ),
    ),
);
```

Resource example
```php
$serviceLocator = $this->getApplication()->getServiceLocator();
$objectManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

$filterManager = $serviceLocator->get('ZfDoctrineQueryBuilderFilterManagerOrm');
$orderByManager = $serviceLocator->get('ZfDoctrineQueryBuilderOrderByManagerOrm');

$queryBuilder = $objectManager->createQueryBuilder();
$queryBuilder->select('row')
    ->from($entity, 'row')
;

$metadata = $objectManager->getMetadataFactory()->getAllMetadata();
$filterManager->filter($queryBuilder, $metadata[0], $_GET['filters']);
$orderByManager->orderBy($queryBuilder, $metadata[0], $_GET['orderBy']);

$result = $queryBuilder->getQuery()->getResult();
```


Filters
-------

Filters are not simple key/value pairs.  Filters are a key-less array of query definitions.  Each
query definition is an array and the array values vary for each query type.

Each query definition requires at a minimum a 'type'.  A type references the configuration key such as 'eq', 'neq', 'between'.

Each query definition requires at a minimum a 'field'.  This is the name of a field on the target entity.

Each query definition may specify 'where' with values of either 'and', 'or'.

Embedded logic such as and(x or y) is supported through AndX and OrX query types.

### Building HTTP GET query:

Javascript Example:

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
------------------

It is possible to query collections by relations - just supply the relation name as `fieldName` and
identifier as `value`.

Assuming we have defined 2 entities, `User` and `UserGroup`...

```php
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
```

```php
/**
 * @Entity
 */
class UserGroup {}
```

find all users that belong to UserGroup id #1 by querying the user resource with the following query:

```php
    array('type' => 'eq', 'field' => 'group', 'value' => '1')
```


Format of Date Fields
---------------------

When a date field is involved in a query you may specify the format of the date using PHP date
formatting options.  The default date format is `Y-m-d H:i:s` If you have a date field which is
just `Y-m-d`, then add the format to the query.

```php
    'format' => 'Y-m-d',
    'value' => '2014-02-04',
```


Joining Entities and Aliasing Queries
-------------------------------------

There is an available ORM Query Type for Inner Join so for every query type there is an optional `alias`.
The default alias is 'row' and refers to the entity at the heart of the REST resource.  There is not a filter to add other entities to the return data.  That is, only the original target resource, by default 'row', will be returned regardless of what filters or order by are applied through this module.

Inner Join is not included by default in the ```zf-doctrine-querybuilder.global.php.dist```

This example joins the report field through the inner join already defined on the row entity then filters
for `r.id = 2`:

```php
    array('type' => 'innerjoin', 'field' => 'report', 'alias' => 'r'),
    array('type' => 'eq', 'alias' => 'r', 'field' => 'id', 'value' => '2')
```

You can inner join tables from an inner join using `parentAlias`:

```php
    array('type' => 'innerjoin', 'parentAlias' => 'r', 'field' => 'owner', 'alias' => 'o'),
```

To enable inner join add this to your configuration.

```php
    'zf-doctrine-querybuilder-filter-orm' => array(
        'invokables' => array(
            'innerjoin' => 'ZF\Apigility\Doctrine\Server\Collection\Filter\ORM\InnerJoin',
        ),
    ),
```


Available Filter Types
---------------------

### ORM and ODM

Equals:

```php
    array('type' => 'eq', 'field' => 'fieldName', 'value' => 'matchValue')
```

Not Equals:

```php
    array('type' => 'neq', 'field' => 'fieldName', 'value' => 'matchValue')
```

Less Than:

```php
    array('type' => 'lt', 'field' => 'fieldName', 'value' => 'matchValue')
```

Less Than or Equals:

```php
    array('type' => 'lte', 'field' => 'fieldName', 'value' => 'matchValue')
```

Greater Than:

```php
    array('type' => 'gt', 'field' => 'fieldName', 'value' => 'matchValue')
```

Greater Than or Equals:

```php
    array('type' => 'gte', 'field' => 'fieldName', 'value' => 'matchValue')
```

Is Null:

```php
    array('type' => 'isnull', 'field' => 'fieldName')
```

Is Not Null:

```php
    array('type' => 'isnotnull', 'field' => 'fieldName')
```

Note: Dates in the In and NotIn filters are not handled as dates.  It is recommended you use multiple Equals statements instead of these filters.

In:

```php
    array('type' => 'in', 'field' => 'fieldName', 'values' => array(1, 2, 3))
```

NotIn:

```php
    array('type' => 'notin', 'field' => 'fieldName', 'values' => array(1, 2, 3))
```

Between:

```php
    array('type' => 'between', 'field' => 'fieldName', 'from' => 'startValue', 'to' => 'endValue')
```

Like (`%` is used as a wildcard):

```php
    array('type' => 'like', 'field' => 'fieldName', 'value' => 'like%search')
```

### ORM Only

AndX:

In AndX queries, the `conditions` is an array of query types for any of those described
here.  The join will always be `and` so the `where` parameter inside of conditions is
ignored.  The `where` parameter on the AndX query type is not ignored.

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

OrX:

In OrX queries, the `conditions` is an array of query types for any of those described
here.  The join will always be `or` so the `where` parameter inside of conditions is
ignored.  The `where` parameter on the OrX query type is not ignored.

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

### ODM Only

Regex:

```php
    array('type' => 'regex', 'field' => 'fieldName', 'value' => '/.*search.*/i')
```


Available Order By Type
---------------------

Field:

```php
    array('type' => 'field', 'field' => 'fieldName', 'direction' => 'desc');
```
