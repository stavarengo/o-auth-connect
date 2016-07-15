<?php
/**
 * irmo Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\Controller\Action\OAuthConnect;

use Interop\Container\ContainerInterface;
use Sta\Commons\CryptQueryParam;
use Sta\Commons\InternalRoute;
use Sta\OAuthConnect\Controller\Action\AbstractAction;
use Sta\OAuthConnect\OAuthService\OAuthServiceInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Stdlib\Parameters;

/**
 * @package Web\Controller\Action\Facebook
 */
class AskForAuthorization extends AbstractAction
{
    /**
     * @var \Zend\Mvc\Router\RouteInterface
     */
    protected $router;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * AskForAuthorization constructor.
     *
     * @param \Zend\Mvc\Router\RouteInterface $router
     */
    public function __construct(\Zend\Mvc\Router\RouteInterface $router, ContainerInterface $container)
    {
        $this->router    = $router;
        $this->container = $container;
    }

    /**
     * Executa a ação.
     *
     * @return mixin
     */
    public function execute()
    {
        $oAuthServiceName             = $this->params()->fromQuery('oAuthService');
        $redirectAfterResponse        = $this->params()->fromQuery('redirectAfterResponse');
        $scopes                       = $this->params()->fromQuery('scopes');
        $scopes                       = ($scopes ? explode(',', $scopes) : []);
        $routeToRedirectAfterResponse = null;

        if (!$oAuthServiceName) {
            return $this->error('The query param "oAuthService" is required.');
        }

        if ($redirectAfterResponse) {
            if ($decrypt_ = CryptQueryParam::decrypt_($redirectAfterResponse)) {
                $routeToRedirectAfterResponse = new InternalRoute($decrypt_);
            }
        }

        AskForAuthorizationResponse::storeWhereToRedirectAfterResponse($routeToRedirectAfterResponse);

        /** @var OAuthServiceInterface $oauthService */
        $oauthService         = $this->container->get('Sta\OAuthConnect\OAuthService\\' . ucfirst($oAuthServiceName));
        $callbackUrl = AskForAuthorizationResponse::getCallbackUrl($this->getController(), $oauthService, $scopes);

        $authorizeRedirectUri = $oauthService->getUrlToAskAuthorization($callbackUrl, $scopes);

        return $this->getController()->redirect()->toUrl($authorizeRedirectUri);
    }

    private function error($error)
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $response->setStatusCode(400);
        $response->setContent(
            json_encode(
                [
                    'status' => '400',
                    'detail' => $error,
                ]
            )
        );

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-type', 'application/json; charset=utf-8');

        return $response;
    }
}
