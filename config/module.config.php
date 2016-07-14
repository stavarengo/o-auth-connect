<?php

return [
    'service_manager' => [
        'abstract_factories' => [
            \Sta\OAuthConnect\OAuthService\AbstractFactory::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            \Web\Controller\Action\Facebook\AskForAuthorization::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'routes' => [
        'web' => [
            'type' => 'Literal',
            'options' => [
                'route' => '/sta/oauth-connect',
                'defaults' => [
                    'controller' => \Web\Controller\Action\Facebook\AskForAuthorization::class,
                ],
            ],
            'may_terminate' => false,
            'child_routes' => [
                'ask' => [
                    'type' => 'Liberal',
                    'options' => [
                        'route' => '/ask',
                        'defaults' => [
                            'action' => 'ask-for-authorization',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
