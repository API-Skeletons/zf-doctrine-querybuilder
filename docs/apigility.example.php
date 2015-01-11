<?php

/**
 * An example for zf-doctrine-querybuilder
 *
 * This example uses arrays for multiple filter and orderby plugin managers
 * because it's possible to break your use of filters into component plugin
 * managers so as example one plugin manager could implement OrX & AndX
 * and a second plugin manager could implement eq, neq
 */

namespace ApplicationApi;

use ZF\Apigility\Provider\ApigilityProviderInterface;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use Zend\Mvc\MvcEvent;

class Module implements ApigilityProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $services = $application->getServiceManager();
        $sharedEvents = $services->get('SharedEventManager');

        $sharedEvents->attach('ZF\Apigility\Doctrine\DoctrineResource',
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE, function ($e) use ($application)
            {
                $objectManager = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');
                $metadata = $objectManager->getMetadataFactory()->getAllMetadata();

                // Fetch filter criteria
                $request = $application->getRequest()->getQuery()->toArray();
                if (!isset($request['query'])) {
                    return;
                }

                // Allow multiple filter managers based on entity
                $filterManagers = array();
                $orderByManagers = array();
                switch ($e->getEntity()) {
                    // Don't filter album queries; for example
                    case 'Db\Entity\Album':
                        return;
                        break;
                    // Do apply ZF Doctrine QueryBuilder
                    case 'Db\Entity\Artist':
                        $filterManagers[] = $application->getServiceManager()->get('ZfDoctrineQueryBuilderFilterManagerOrm');
                        $orderByManagers[] = $application->getServiceManager()->get('ZfDoctrineQueryBuilderOrderByManagerOrm');
                        break;
                    default:
                        return;
                        break;
                }

                foreach ($filterManagers as $filterManager) {
                    $filterManager->filter(
                        $e->getQueryBuilder(),
                        $metadata[0],
                        $request['filter']
                    );
                }

                foreach ($orderByManagers as $orderByManager) {
                    $orderByManager->orderBy(
                        $e->getQueryBuilder(),
                        $metadata[0],
                        $request['orderBy']
                    );
                }
            }
        );
    }

    // Continue with other module functions...