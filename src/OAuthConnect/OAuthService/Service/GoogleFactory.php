<?php
namespace Sta\OAuthConnect\OAuthService\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Sta\OAuthConnect\Exception\GoogleAuthConfigFileNotFound;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
class GoogleFactory extends BaseFactory
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
        $client = new \Google_Client();

        $oAuthServiceConfig = $this->getOAuthServiceConfig($serviceLocator, 'google');
        if (isset($oAuthServiceConfig['authConfigFile'])) {
            if (!file_exists($oAuthServiceConfig['authConfigFile'])) {
                throw new GoogleAuthConfigFileNotFound(
                    'The file ' . $oAuthServiceConfig['authConfigFile'] . ' was not found.'
                );
            }
            $client->setAuthConfigFile($oAuthServiceConfig['authConfigFile']);
        } else {
            $oAuthServiceConfig = $this->checkRequiredConfigs(
                $serviceLocator,
                [
                    'clientId',
                    'clientSecret',
                ],
                'google'
            );

            $client->setClientId($oAuthServiceConfig['clientId']);
            $client->setClientSecret($oAuthServiceConfig['clientSecret']);

            /** @var \Zend\Mvc\Router\RouteInterface $router */
            $router      = $serviceLocator->get('Router');
            $redirectUri = $router->assemble(
                [],
                [
                    'name' => 'sta/oAuthConnect/response',
                    'force_canonical' => true,
                ]
            );
            $client->setRedirectUri($redirectUri);
        }

        return new Google($client);
    }
}
