<?php
namespace Sta\OAuthConnect\OAuthService\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
class FacebookFactory extends BaseFactory
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $thisOAuthServiceConfig = $this->checkRequiredConfigs(
            $serviceLocator,
            [
                'appId',
                'appSecret',
            ],
            'facebook'
        );

        return new Facebook(
            $thisOAuthServiceConfig['appId'],
            $thisOAuthServiceConfig['appSecret']
        );
    }
}
