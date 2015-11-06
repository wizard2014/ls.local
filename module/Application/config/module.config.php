<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'about-us' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/about-us',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'about-us',
                    ),
                ),
            ),
            'paperwork' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/paperwork',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'paperwork',
                    ),
                ),
            ),
            'about-romania' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/about-romania',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'about-romania',
                    ),
                ),
            ),
            'attention' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/attention',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'attention',
                    ),
                ),
            ),
            'contacts' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/contacts',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'contacts',
                    ),
                ),
            ),
            'reviews' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/reviews',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'reviews',
                    ),
                ),
            ),
            'aside' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/aside',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'aside',
                    ),
                ),
            ),
            'order' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/order',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'order',
                    ),
                ),
            ),
            'upload' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/upload[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Upload',
                        'action'        => 'index',
                    ),
                ),
            ),
            'edit' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/edit[/:action[/:id]]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'edit',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Upload' => 'Application\Controller\UploadController',
            'Application\Controller\Edit'   => 'Application\Controller\EditController'
        ),
        'factories' => array(
            'Application\Controller\Index' => 'Application\Factory\Controller\IndexControllerFactory',
            'Application\Controller\Cron'  => 'Application\Factory\Controller\CronControllerFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/admin'            => __DIR__ . '/../view/layout/admin.phtml',
            'layout/image'            => __DIR__ . '/../view/layout/image.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array (
            'ViewJsonStrategy'
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'cronroute' => array(
                    'options' => array(
                        'route'    => 'updatedocs',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                            'action'     => 'index'
                        ),
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'link_to_order_entity' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),
            'main_content_entity' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'link_to_order_entity',
                )
            )
        )
    ),
);
