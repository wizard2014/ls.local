<?php

namespace Application\Factory\Controller;

use Application\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = $serviceLocator->getServiceLocator();

        $controller = new IndexController();

        $em    = $serviceManager->get('Doctrine\ORM\EntityManager');
        $cache = $serviceManager->get('filesystem');

        if (!$cache->hasItem('doc')) {
            $doc = $em->getRepository('Application\Entity\LinkToOrder')->findAll();

            $cache->setItem('doc', serialize(array_reverse($doc)));
        }

        if (!$cache->hasItem('vid')) {
            $vid = $em->getRepository('Application\Entity\YoutubeVideo')->findAll();

            $cache->setItem('vid', serialize(array_reverse($vid)));
        }

        $controller->setCache($serviceManager->get('memory'));
        $controller->setDoc(unserialize($cache->getItem('doc')));
        $controller->setVideo(unserialize($cache->getItem('vid')));

        return $controller;
    }
}
