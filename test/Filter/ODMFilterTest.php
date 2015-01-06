<?php

namespace ZFTest\Doctrine\QueryBuilder\Filter;

use Zend\Http\Request;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use DateTime;
use DbMongo\Document;

class ODMFilterTest extends AbstractHttpControllerTestCase
{
    private function countResult($filters, $entity = 'DbMongo\Document\Meta')
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $filterManager = $serviceManager->get('ZfDoctrineQueryBuilderFilterManagerOdm');
        $objectManager = $serviceManager->get('doctrine.documentmanager.odm_default');
        $queryBuilder = $objectManager->createQueryBuilder($entity);
        # NOTE:  the metadata is an array with one element in testing :\

        $metadata = $objectManager->getMetadataFactory()->getAllMetadata();

        $filterManager->filter($queryBuilder, $metadata[0], $filters);

        $result = $queryBuilder->getQuery()->execute();
        return sizeof($result);
    }

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/application.config.php'
        );
        parent::setUp();

        $config = $this->getApplication()->getConfig();
        $config = $config['doctrine']['connection']['odm_default'];

        $connection = new \MongoClient('mongodb://' . $config['server'] . ':' . $config['port']);
        $db = $connection->{$config['dbname']};
        $collection = $db->meta;
        $collection->remove();

        $serviceManager = $this->getApplication()->getServiceManager();
        $objectManager = $serviceManager->get('doctrine.documentmanager.odm_default');

        $meta1 = new Document\Meta;
        $meta1->setName('MetaOne');
        $meta1->setCreatedAt(new DateTime('2011-12-18 13:17:17'));
        $objectManager->persist($meta1);

        $meta2 = new Document\Meta;
        $meta2->setName('MetaTwo');
        $meta2->setCreatedAt(new DateTime('2014-12-18 13:17:17'));
        $objectManager->persist($meta2);

        $meta3 = new Document\Meta;
        $meta3->setName('MetaThree');
        $meta3->setCreatedAt(new DateTime('2012-12-18 13:17:17'));
        $objectManager->persist($meta3);

        $meta4 = new Document\Meta;
        $meta4->setName('MetaFour');
        $meta4->setCreatedAt(new DateTime('2013-12-18 13:17:17'));
        $objectManager->persist($meta4);

        $meta5 = new Document\Meta;
        $meta5->setName('MetaFive');
        $objectManager->persist($meta5);

        $objectManager->flush();
    }

    public function testEquals()
    {
        $filters = array(
            array('field' =>'name', 'type'=>'eq', 'value' => 'MetaOne'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'eq', 'value' => '2014-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'eq', 'value' => '2014-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'eq', 'value' => '2012-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(2, $this->countResult($filters));
    }

    public function testNotEquals()
    {
        $filters = array(
            array('field' =>'name', 'type'=>'neq', 'value' => 'MetaOne'),
        );

        $this->assertEquals(4, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'neq', 'value' => '2014-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(4, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'neq', 'value' => '2014-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'neq', 'value' => '2012-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(3, $this->countResult($filters));
    }

    public function testLessThan()
    {
        $filters = array(
            array('field' =>'createdAt', 'type'=>'lt', 'value' => '2014-01-01', 'format' => 'Y-m-d'),
        );

        $this->assertEquals(3, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'lt', 'value' => '2013-12-18 13:17:17'),
        );

        $this->assertEquals(2, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'lt', 'value' => '2013-12-18 13:17:17'),
            array('field' =>'name', 'where' => 'or', 'type' => 'eq', 'value'=>'MetaTwo'),
        );

        $this->assertEquals(3, $this->countResult($filters));
    }

    public function testLessThanOrEquals()
    {
        $filters = array(
            array('field' =>'createdAt', 'type'=>'lte', 'value' => '2011-12-20', 'format' => 'Y-m-d'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'type'=>'lte', 'value' => '2011-12-18 13:17:16'),
        );

        $this->assertEquals(0, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'lte', 'value' => '2013-12-18 13:17:17'),
        );

        $this->assertEquals(3, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'lte', 'value' => '2013-12-18 13:17:17'),
            array('field' =>'name', 'where' => 'or', 'type' => 'eq', 'value'=>'MetaTwo'),
        );

        $this->assertEquals(4, $this->countResult($filters));
    }

    public function testGreaterThan()
    {
        $filters = array(
            array('field' =>'createdAt', 'type'=>'gt', 'value' => '2014-01-01', 'format' => 'Y-m-d'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'gt', 'value' => '2013-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'gt', 'value' => '2013-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'gt', 'value' => '2012-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(1, $this->countResult($filters));
    }

    public function testGreaterThanOrEquals()
    {
        $filters = array(
            array('field' =>'createdAt', 'type'=>'gte', 'value' => '2014-12-18 13:17:17'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'type'=>'gte', 'value' => '2014-12-18 13:17:18'),
        );

        $this->assertEquals(0, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'gte', 'value' => '2013-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(2, $this->countResult($filters));

        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'gte', 'value' => '2013-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'gte', 'value' => '2012-12-18 13:17:17', 'format' => 'Y-m-d H:i:s'),
        );

        $this->assertEquals(3, $this->countResult($filters));
    }

    public function testIsNull()
    {
        $filters = array(
            array('field' =>'createdAt', 'type'=>'isnull'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'isnull'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type' => 'isnull'),
            array('field' =>'name', 'where' => 'or', 'type' => 'eq', 'value'=>'MetaOne'),
        );

        $this->assertEquals(2, $this->countResult($filters));
    }

    public function testIsNotNull()
    {
        $filters = array(
            array('field' =>'createdAt', 'type'=>'isnotnull'),
        );

        $this->assertEquals(4, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'isnotnull'),
        );

        $this->assertEquals(4, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type' => 'isnotnull'),
            array('field' =>'name', 'where' => 'or', 'type' => 'eq', 'value'=>'MetaFive'),
        );

        $this->assertEquals(5, $this->countResult($filters));
    }

    public function testIn()
    {
        // Date handling in IN and NOTIN doesn't seem to work at all, so just test with strings

        $filters = array(
            array('field' =>'name', 'type'=>'in', 'values' => array('MetaOne', 'MetaTwo')),
        );

        $this->assertEquals(2, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'type'=>'in', 'values' => array('MetaOne'), 'where' => 'and'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'type'=>'in', 'values' => array('MetaOne'), 'where' => 'or'),
        );

        // count is 2 because null is not counted in a notin
        $this->assertEquals(1, $this->countResult($filters));
    }

    public function testNotIn()
    {
        $filters = array(
            array('field' =>'name', 'type'=>'notin', 'values' => array('MetaOne', 'MetaTwo')),
        );

        $this->assertEquals(3, $this->countResult($filters));


        // Test date field
        $filters = array(
            array('field' =>'name', 'where' => 'and', 'type'=>'notin', 'values' => array('MetaOne')),
        );

        $this->assertEquals(4, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'where' => 'or', 'type'=>'notin', 'values' => array('MetaTwo')),
        );

        // count is 2 because null is not counted in a notin
        $this->assertEquals(4, $this->countResult($filters));
    }

    public function testBetween()
    {
        $filters = array(
            array('field' =>'createdAt', 'where' => 'and', 'type'=>'between', 'from' => '2012-12-15', 'to' => '2013-01-01', 'format' => 'Y-m-d'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'where' => 'or', 'type'=>'between', 'from' => '2010-12-15', 'to' => '2013-01-01', 'format' => 'Y-m-d'),
        );

        $this->assertEquals(2, $this->countResult($filters));


        $filters = array(
            array('field' =>'createdAt', 'type'=>'between', 'from' => '2010-12-15', 'to' => '2013-01-01', 'format' => 'Y-m-d'),
        );

        $this->assertEquals(2, $this->countResult($filters));
    }

    public function testLike()
    {
        $filters = array(
            array('field' =>'name', 'type'=>'like', 'value' => 'Meta%'),
        );

        $this->assertEquals(5, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'type'=>'like', 'value' => '%Two'),
        );

        $this->assertEquals(1, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'where' => 'and', 'type'=>'like', 'value' => '%eta%'),
        );

        $this->assertEquals(5, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'where' => 'or', 'type' => 'like', 'value' => 'MetaT%'),
            array('field' =>'name', 'where' => 'or', 'type' => 'like', 'value'=>'MetaF%'),
        );

        $this->assertEquals(4, $this->countResult($filters));
    }

    public function testRegex()
    {
        $filters = array(
            array('field' =>'name', 'type'=>'regex', 'value' => '/.*T.*$/'),
        );

        $this->assertEquals(2, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'where' => 'or', 'type'=>'regex', 'value' => '/.*T.*$/'),
        );

        $this->assertEquals(2, $this->countResult($filters));


        $filters = array(
            array('field' =>'name', 'where' => 'and', 'type'=>'regex', 'value' => '/.*T.*$/'),
        );

        $this->assertEquals(2, $this->countResult($filters));
    }
}
