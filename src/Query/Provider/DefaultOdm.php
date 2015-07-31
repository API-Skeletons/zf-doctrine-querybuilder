<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\QueryProviderInterface;
use ZF\Apigility\Doctrine\Server\Paginator\Adapter\DoctrineOdmAdapter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Rest\ResourceEvent;

class DefaultOdm implements QueryProviderInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set the object manager
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $request = $this->getServiceLocator()
            ->getServiceLocator()->get('Application')->getRequest()->getQuery()->toArray();

        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);

        if (isset($request[$this->getFilterKey()])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $filterManager = $this->getServiceLocator()
                ->getServiceLocator()->get('ZfDoctrineQueryBuilderFilterManagerOdm');
            $filterManager->filter(
                $queryBuilder,
                $metadata[0],
                $request[$this->getFilterKey()]
            );
        }

        if (isset($request[$this->getOrderByKey()])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $orderByManager = $this->getServiceLocator()
                ->getServiceLocator()->get('ZfDoctrineQueryBuilderOrderByManagerOdm');
            $orderByManager->orderBy(
                $queryBuilder,
                $metadata[0],
                $request[$this->getOrderByKey()]
            );
        }

        return $queryBuilder;
    }

    /**
     * @param   $queryBuilder
     *
     * @return DoctrineOdmAdapter
     */
    public function getPaginatedQuery($queryBuilder)
    {
        $adapter = new DoctrineOdmAdapter($queryBuilder);

        return $adapter;
    }

    /**
     * @param   $entityClass
     *
     * @return int
     */
    public function getCollectionTotal($entityClass)
    {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);
        $count = $queryBuilder->getQuery()->execute()->count();

        return $count;
    }

    /**
     * @return string
     */
    protected function getFilterKey()
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('Config');

        if (isset($config['zf-doctrine-querybuilder-options']['filter_key'])) {
            $filterKey = $config['zf-doctrine-querybuilder-options']['filter_key'];
        } else {
            $filterKey = 'filter';
        }

        return $filterKey;
    }

    /**
     * @return string
     */
    protected function getOrderByKey()
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('Config');

        if (isset($config['zf-doctrine-querybuilder-options']['order_by_key'])) {
            $orderByKey = $config['zf-doctrine-querybuilder-options']['order_by_key'];
        } else {
            $orderByKey = 'order-by';
        }

        return $orderByKey;
    }
}
