<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',

        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        'role_providers'    => array(
            // using an object repository (entity repository) to load all roles into our ACL
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => 'User\Entity\Role',
            ),
        ),

        'guards'            => array(
            'BjyAuthorize\Guard\Controller' => array(
                // Раздел 1
                // разрешения для всех посетителей сайта (включая незарегистрированных)
                array(
                    'controller' => 'Application\Controller\Index',
                    'action'     => array('index', 'about-us', 'paperwork', 'about-romania', 'attention', 'contacts', 'reviews', 'aside', 'order'),
                    'roles'      => array(),
                ),
                // конец раздела 1

                // Раздел 2
                // разграничение доступа к модулю ZfcUser для неавторизовавшихся и авторизовавшихся
                array(
                    'controller' => 'zfcuser',
                    'action'     => array('index', 'login', 'authenticate'),
                    'roles'      => array('guest'),
                ),
                array(
                    'controller' => 'zfcuser',
                    'action'     => array('logout'),
                    'roles'      => array('user'),
                ),
                // конец раздела 2

                // Раздел 3
                // разрешения для авторизованных пользователей с ролью ADMIN
                array(
                    'controller' => array(
                        'Application\Controller\Edit',
                        'Application\Controller\Upload',
                    ),
                    'roles'      => array('admin'),
                ),
            ),
        ),

    ),
);