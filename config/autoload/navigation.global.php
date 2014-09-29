<?php
/**
 * Global navigation config
 */

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Главная',
                'route' => 'home',
            ),
            array(
                'label' => 'О нас',
                'route' => 'about-us',
            ),
            array(
                'label' => 'Схема оформления',
                'route' => 'paperwork',
            ),
            array(
                'label' => 'О Румынии',
                'route' => 'about-romania',
            ),
            array(
                'label' => 'Внимание',
                'route' => 'attention',
            ),
            array(
                'label' => 'Контакты',
                'route' => 'contacts',
            ),
//            array(
//                'label' => 'Отзывы',
//                'route' => 'reviews',
//            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
);