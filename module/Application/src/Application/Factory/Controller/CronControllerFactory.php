<?php

namespace Application\Factory\Controller;

use Application\Controller\CronController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CronControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CronController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controller = new CronController();

        $cache = $serviceLocator->getServiceLocator()->get('filesystem');
        $controller->setCache($cache);

        return $controller;
    }
}
