<?php
/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\OAuthService\Service;

use Sta\OAuthConnect\OAuthService\AuthorizationResult;
use Sta\OAuthConnect\OAuthService\OAuthServiceInterface;

class Google implements OAuthServiceInterface
{
    /**
     * @var \Google_Client
     */
    protected $client;

    /**
     * Google constructor.
     */
    public function __construct(\Google_Client $client)
    {
        $this->client = $client;
    }


    /**
     * Verifica todas as dependencias para este serviço funcionar estão instaladas.
     *
     * @return true|string[]
     *      Retorna true se está tudo ok, ou um string[] com a lista das dependencias que estão faltando.
     */
    public function checkDependencies()
    {
        $dependencies = [];
        if (!class_exists('\Google_Client')) {
            $dependencies[] = 'Google API Client PHP Library: https://developers.google.com/api-client-library/php/start/get_started';
        } else {
            $minVersion = '5.2.1';
            if (!version_compare($minVersion, \Facebook\Facebook::VERSION, '>=')) {
                $dependencies[] = 'Facebook SDK must be version 5.2.0 or great.';
            }
        }

        return ($dependencies ? $dependencies : true);
    }

    /**
     * Retorna todos os scopoos que podemos pedir.
     * Qualquer scopo alem dos listados aqui não devem ser pedidos pois podem ser recusados pelo serviço de OAuth.
     *
     * @return string[]
     */
    public function getAllScopesWeCanAsk()
    {
        // TODO: Implement getAllScopesWeCanAsk() method.
    }

    /**
     * Scopos que sempre devem ser pedidos.
     *
     * @return string[]
     */
    public function getMinimumScopes()
    {
        // TODO: Implement getMinimumScopes() method.
    }

    /**
     * Scopos que o usuário sempre deve autorizar caso contrário não vamos aceitar o token de autorização dele.
     *
     * @return string[]
     */
    public function getRequiredScopes()
    {
        // TODO: Implement getRequiredScopes() method.
    }

    /**
     *
     * @return mixed
     */
    public function getUrlToAskAuthorization($callbackUrl, $scopes)
    {
        $this->client->setScopes($scopes);

        return $this->client->createAuthUrl();
    }

    /**
     * @param $scopes
     *
     * @return AuthorizationResult
     */
    public function isAuthorized($scopes)
    {
        $authorizedResult = new AuthorizationResult();

        if (isset($_GET['code'])) {
            $accessToken = $this->client->authenticate($_GET['code']);

            $authorizedResult->setAccessToken($accessToken);
            $authorizedResult->setRequiredScopes($scopes);
            $authorizedResult->setAuthorizedScopes($scopes);
        }

        return $authorizedResult;
    }

    /**
     * @return \Google_Client
     */
    public function getServiceClient()
    {
        return $this->client;
    }
}
