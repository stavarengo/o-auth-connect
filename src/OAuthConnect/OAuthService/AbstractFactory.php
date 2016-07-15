<?php

namespace Sta\OAuthConnect\OAuthService;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Sta\OAuthConnect\Exception\MissingOAuthServiceDependencies;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
class AbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $serviceNamespace = __NAMESPACE__ . '\\';

        return strpos($requestedName, $serviceNamespace) === 0;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->__invoke($serviceLocator, $requestedName);
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
        $serviceClassName = $requestedName;

        if (!class_exists($serviceClassName)) {
            throw new \Sta\OAuthConnect\Exception\InvalidThirdPartyName(
                'Invalid "thirdPartyName". We do not support the service "' . $serviceClassName . '".'
            );
        }

        if ($container->has($serviceClassName)) {
            $service = $container->get($serviceClassName);
        } else {
            $service = new $serviceClassName();
        }

        if (!($service instanceof OAuthServiceInterface)) {
            throw new \Sta\OAuthConnect\Exception\InvalidThirdPartyName(
                'The service "' . $serviceClassName .
                '" does not implement the interface "' . OAuthServiceInterface::class . '".'
            );
        }

        $checkDependencies = $service->checkDependencies();
        if ($checkDependencies !== true) {
            throw new MissingOAuthServiceDependencies(
                'Please, make sure you have all of this requeriments ok before you try to use the ' .
                $serviceClassName . ' OAuth Service: ' . implode(', ', $checkDependencies)
            );
        }

        return $service;
    }
}
