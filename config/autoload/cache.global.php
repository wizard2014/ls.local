<?php

return array(
    'caches' => array(
        'filesystem' => array(
            'adapter' => array(
                'name'    =>'filesystem', // filesystem, memory, apc are working
                'options' => array('ttl' => 2592000), //28800
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            ),
        ),
        'memory' => array(
            'adapter' => array(
                'name' =>'memory',
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            ),
        ),
    ),
);