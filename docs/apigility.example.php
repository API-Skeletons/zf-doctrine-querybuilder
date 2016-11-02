<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

/**
 * An example for zf-doctrine-querybuilder
 *
 * This example uses arrays for multiple filter and orderby plugin managers
 * because it's possible to break your use of filters into component plugin
 * managers so as example one plugin manager could implement OrX & AndX
 * and a second plugin manager could implement eq, neq
 */

namespace ApplicationApi;

use Zend\Mvc\MvcEvent;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $services = $application->getServiceManager();
        $sharedEvents = $services->get('SharedEventManager');

        $sharedEvents->attach(
            'ZF\Apigility\Doctrine\DoctrineResource',
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            function (DoctrineResourceEvent $e) use ($application) {
                $services = $application->getServiceManager();
                $objectManager = $services->get('doctrine.entitymanager.orm_default');
                $metadata = $objectManager->getMetadataFactory()->getAllMetadata();

                // Fetch filter criteria
                $request = $application->getRequest()->getQuery()->toArray();
                if (! isset($request['query'])) {
                    return;
                }

                // Allow multiple filter managers based on entity
                $filterManagers = [];
                $orderByManagers = [];
                switch ($e->getEntity()) {
                    // Don't filter album queries; for example
                    case 'Db\Entity\Album':
                        break;
                    // Do apply ZF Doctrine QueryBuilder
                    case 'Db\Entity\Artist':
                        $filterManagers[] = $services->get('ZfDoctrineQueryBuilderFilterManagerOrm');
                        $orderByManagers[] = $services->get('ZfDoctrineQueryBuilderOrderByManagerOrm');
                        break;
                    default:
                        // Define here default behaviour
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
