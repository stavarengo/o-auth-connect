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
            $rawAccessToken = $this->client->authenticate($_GET['code']);
            if(is_string($rawAccessToken)) {
                $accessToken     = json_decode($rawAccessToken)->access_token;
            } else if (is_array($rawAccessToken)) {
                $accessToken = $rawAccessToken['access_token'];
            } else {
                $accessToken = $rawAccessToken->access_token;
            }

            $oAuth            = new \Google_Service_Oauth2($this->client);
            $tokenInfo        = $oAuth->tokeninfo(
                [
                    'access_token' => $accessToken
                ]
            );
            $authorizedScopes = explode(' ', $tokenInfo->getScope());

            $authorizedResult->setAccessToken($rawAccessToken);
            $authorizedResult->setRequiredScopes($scopes);
            $authorizedResult->setAuthorizedScopes($authorizedScopes);
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
