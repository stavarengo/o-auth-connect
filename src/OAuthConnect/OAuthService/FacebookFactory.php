<?php
namespace Sta\OAuthConnect\OAuthService;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Sta\OAuthConnect\Exception\MissingConfiguration;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
class FacebookFactory implements FactoryInterface
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
        $config = $serviceLocator->get('config');

        if (!isset($config['sta']['o-auth-connect']['o-auth-services']['facebook']['appId'])) {
            throw new MissingConfiguration(
                'Missing configuration: "$config[\'sta\'][\'o-auth-connect\'][\'o-auth-services\'][\'facebook\'][\'appId\']"'
            );
        }
        if (!isset($config['sta']['o-auth-connect']['o-auth-services']['facebook']['appSecret'])) {
            throw new MissingConfiguration(
                'Missing configuration: "$config[\'sta\'][\'o-auth-connect\'][\'o-auth-services\'][\'facebook\'][\'appSecret\']"'
            );
        }

        return new Facebook(
            $config['sta']['o-auth-connect']['o-auth-services']['facebook']['appId'],
            $config['sta']['o-auth-connect']['o-auth-services']['facebook']['appSecret']
        );
    }

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // TODO: Implement __invoke() method.
    }
}
