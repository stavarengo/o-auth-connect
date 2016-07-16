<?php
namespace Sta\OAuthConnect\OAuthService\Service;

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
abstract class BaseFactory implements FactoryInterface
{

    protected function checkRequiredConfigs(ServiceLocatorInterface $serviceLocator, $requiredConfigs, $configEntryName)
    {
        $oAuthServiceConfig = $this->getOAuthServiceConfig($serviceLocator, $configEntryName);

        foreach ($requiredConfigs as $requiredConfig) {
            if (!isset($oAuthServiceConfig[$requiredConfig])) {
                throw new MissingConfiguration(
                    "Missing configuration: \"\$config['sta']['o-auth-connect']['o-auth-services']" .
                    "['$configEntryName']['$requiredConfig']\""
                );
            }
        }

        return $oAuthServiceConfig;
    }

    protected function getOAuthServiceConfig(ServiceLocatorInterface $serviceLocator, $configEntryName)
    {
        $config          = $serviceLocator->get('config');
        $oAuthServices   = $config['sta']['o-auth-connect']['o-auth-services'];

        if (!isset($oAuthServices[$configEntryName])) {
            throw new MissingConfiguration(
                "Missing configuration: \"\$config['sta']['o-auth-connect']['o-auth-services']['$configEntryName']\""
            );
        }

        return $oAuthServices[$configEntryName];
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
        return $this->createService($container);
    }
}
