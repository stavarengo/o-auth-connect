<?php
/**
 * Created by PhpStorm.
 * User: stavarengo
 * Date: 14/03/19
 * Time: 16:36
 */

namespace Sta\OAuthConnect;


class ConfigProvider
{
    public function __invoke()
    {
        return [
            self::class => $this->getConfig(),
            'dependencies' => $this->getDependencyConfig(),
            'controllers' => $this->getControllersConfig(),
            'view_manager' => $this->getViewManagerConfig(),
            'router' => $this->getRouterConfig(),
        ];
    }

    public function getConfig()
    {
        return [
            'o-auth-services' => [
            ],
            'custom-services' => [
            ],
        ];
    }

    public function getDependencyConfig()
    {
        return [
            'abstract_factories' => [
                \Sta\OAuthConnect\OAuthService\AbstractFactory::class,
            ],
            'factories' => [
                \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorization::class => \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorizationFactory::class,
                \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorizationResponse::class => \Sta\OAuthConnect\Controller\Action\OAuthConnect\AskForAuthorizationResponseFactory::class,
                \Sta\OAuthConnect\OAuthService\Service\Facebook::class => \Sta\OAuthConnect\OAuthService\Service\FacebookFactory::class,
                \Sta\OAuthConnect\OAuthService\Service\Google::class => \Sta\OAuthConnect\OAuthService\Service\GoogleFactory::class,
            ],
        ];
    }

    public function getControllersConfig()
    {
        return [
            'invokables' => [
                \Sta\OAuthConnect\Controller\OAuthConnectController::class => \Sta\OAuthConnect\Controller\OAuthConnectController::class,
            ],
        ];
    }

    public function getViewManagerConfig()
    {
        return [
            'template_path_stack' => [
                __DIR__ . '/../../view',
            ],
        ];
    }

    public function getRouterConfig()
    {
        return [
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
                                    'type' => 'Segment',
                                    'options' => [
                                        'route' => '/ask/:oAuthService',
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
        ];
    }
}