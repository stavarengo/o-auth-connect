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
        if ($this->isCustomService($container, $requestedName)) {
            return true;
        }
        
        //$serviceNamespace                      = __NAMESPACE__ . '\\';
        //$isOneOfOurOutOfTheBoxSupportedService = strpos($requestedName, $serviceNamespace) === 0;
        return  $this->doWeRecognizeThiRequestedName($requestedName);
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
        $doWeRecognizeThiRequestedName = $this->doWeRecognizeThiRequestedName($requestedName);
        // Correct the namespace
        $shortName = trim(str_replace(__NAMESPACE__, '', $requestedName), '\\');
        if ($this->isCustomService($container, $requestedName)) {
            $serviceClassName = $this->getCustomServices($container)[$shortName];
        } else {
            $serviceClassName = __NAMESPACE__ . '\\Service\\' . ucfirst($shortName);
        }

        if ($container->has($serviceClassName)) {
            $service = $container->get($serviceClassName);
        } else {
            if (!class_exists($serviceClassName)) {
                $serviceName = $serviceClassName;
                $errorMsg    = 'The OAuthService class "' . $serviceName . '" was not found.';
                if ($doWeRecognizeThiRequestedName) {
                    if (isset($shortName)) {
                        $serviceName = $shortName;
                        $errorMsg    = 'Invalid OAuthService. We do not support this service yet: "' . $serviceName . '".' .
                            'See this link to learn how to implement a custom service: ' .
                            'https://github.com/stavarengo/o-auth-connect#adding-custom-services';
                    }
                }
                throw new \Sta\OAuthConnect\Exception\InvalidThirdPartyName($errorMsg);
            }

            $service = new $serviceClassName();
        }


        if (!($service instanceof OAuthServiceInterface)) {
            throw new \Sta\OAuthConnect\Exception\InvalidThirdPartyName(
                'The service "' . $serviceClassName .
                '" must implement the interface "' . OAuthServiceInterface::class . '".'
            );
        }

        $checkDependencies = $service->checkDependencies();
        if ($checkDependencies !== true) {
            throw new MissingOAuthServiceDependencies(
                'Please, make sure you have all of this requirements ok before you try to use the ' .
                $serviceClassName . ' OAuth Service. The requirements are: ' . implode(', ', $checkDependencies)
            );
        }

        return $service;
    }

    /**
     * @param $requestedName
     *
     * @return int
     */
    public function doWeRecognizeThiRequestedName($requestedName)
    {
        $regex                                 = preg_quote(__NAMESPACE__ . '\\', '/') . '[A-Za-z_0-9]+$';
        $isOneOfOurOutOfTheBoxSupportedService = preg_match('/' . $regex . '/', $requestedName);

        return $isOneOfOurOutOfTheBoxSupportedService;
    }

    /**
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return mixed
     */
    public function getCustomServices(ContainerInterface $container)
    {
        $config             = $container->get('Config');
        $oAuthConnectConfig = $config[\Sta\OAuthConnect\ConfigProvider::class];
        $customServices     = $oAuthConnectConfig['custom-services'];

        return $customServices;
    }

    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param $requestedName
     *
     * @return bool
     */
    public function isCustomService(ContainerInterface $container, $requestedName)
    {
        if (!$this->doWeRecognizeThiRequestedName($requestedName)) {
            return false;
        }

        $customServices = $this->getCustomServices($container);

        $customServiceName = trim(str_replace(__NAMESPACE__, '', $requestedName), '\\');

        return isset($customServices[$customServiceName]);
    }
}
