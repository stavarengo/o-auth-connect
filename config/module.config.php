<?php

return [
    'sta' => [
        'o-auth-connect' => [
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            \Sta\OAuthConnect\OAuthService\AbstractFactory::class,
        ],
        'factories' => [
            \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorization::class => \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorizationFactory::class,
            \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorizationResponse::class => \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorizationResponseFactory::class,
            \Sta\OAuthConnect\OAuthService\Facebook::class => \Sta\OAuthConnect\OAuthService\FacebookFactory::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            \Sta\OAuthConnect\Controller\OAuthConnectController::class => \Sta\OAuthConnect\Controller\OAuthConnectController::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'router' => [
        'routes' => [
            'sta' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'oAuthConnect' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => 'sta/oauth-connect',
                            'defaults' => [
                                'controller' => \Sta\OAuthConnect\Controller\OAuthConnectController::class,
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'ask' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/ask',
                                    'defaults' => [
                                        'action' => 'ask-for-authorization',
                                    ],
                                ],
                            ],
                            'response' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/response',
                                    'defaults' => [
                                        'action' => 'ask-for-authorization-response',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
