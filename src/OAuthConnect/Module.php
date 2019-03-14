<?php
/**
 * Created by PhpStorm.
 * User: stavarengo
 * Date: 14/03/19
 * Time: 17:06
 */

namespace Sta\OAuthConnect;


class Module
{
    public function getConfig()
    {
        $provider = new ConfigProvider();
        return [
            ConfigProvider::class => $provider->getConfig(),
            'service_manager' => $provider->getDependencyConfig(),
            'controllers' => $provider->getControllersConfig(),
            'view_manager' => $provider->getViewManagerConfig(),
            'router' => $provider->getRouterConfig(),
        ];
    }
}