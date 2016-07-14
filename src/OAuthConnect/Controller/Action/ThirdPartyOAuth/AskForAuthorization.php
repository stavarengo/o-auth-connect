<?php
/**
 * irmo Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Web\Controller\Action\Facebook;

use App\Mvc\Controller\Action;
use App\Util\CryptQueryParam;
use App\Util\InternalRoute;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\Router\Console\RouteInterface;
use Zend\Stdlib\Parameters;
use Zend\Uri\Http as HttpUri;

/**
 * @package Web\Controller\Action\Facebook
 */
class AskForAuthorization extends Action
{

    /**
     * Executa a ação.
     *
     * @return mixin
     */
    public function execute()
    {
        $facebook = new Facebook();

        $redirectAfterResponse = $this->params()->fromQuery('redirectAfterResponse');
        $scopes                = $this->params()->fromQuery('scopes');
        $rerequest             = $this->params()->fromQuery('rerequest', true);

        $scopes = ($scopes ? explode(',', $scopes) : []);

        $internalRoute = AskForAuthorizationResponse::OPT_ASYNC_AUTHORIZATION;
        if ($redirectAfterResponse) {
            // O JS pode criar um link para o usuário fazer login pedingo que apos o login o usuário seja
            // direcionado para a mesma página onde o usuário estava antes.
            if ($redirectAfterResponse == 'referer') {
                $redirectAfterResponse = $this->getContinueRouteFromReferer();
            }
            if ($decrypt_ = CryptQueryParam::decrypt_($redirectAfterResponse)) {
                $internalRoute = new InternalRoute($decrypt_);
            }
        }

        $socialNetworkCallbackUri = AskForAuthorizationResponse::getAuthorizeUrl(
            $this->getServiceLocator(),
            $internalRoute,
            $scopes
        );
//        $currUser                 = $this->getController()->currUser()->user();
//        (!$currUser || (bool)$currUser->getFacebookAccessToken())
        $authorizeRedirectUri = $facebook->getLoginUrl(
            $socialNetworkCallbackUri,
            $rerequest,
            AskForAuthorizationResponse::getScopes($this->getServiceLocator(), $scopes)
        );

        return $this->getController()->redirect()->toUrl($authorizeRedirectUri);
    }

    /**
     * @param $query
     *
     * @return InternalRoute
     */
    private function getRedirectFromReferer()
    {
        $referer = $this->getController()->urls()->getReferer();
        if (!$referer) {
            return null;
        }

        $refererUri = new HttpUri($referer);
        $currUri    = $this->getRequest()->getUri();

        // Precisamos garantir que apos o login o usuario será redirecionado para um link nosso.
        // Isso evita ataques, pois outros poderiam passar um HTTP_REFERER falso para o usuário fosse
        // redirecionado para um site mirror do nosso após o login.
        if ($currUri->getHost() != $refererUri->getHost() || $currUri->getScheme() != $refererUri->getScheme()) {
            return null;
        }

        $query = [];
        if ($parse_url = parse_url($referer, PHP_URL_QUERY)) {
            parse_str($parse_url, $query);
        }

        $request = new HttpRequest();
        $request->setRequestUri($referer);
        $request->setUri($refererUri);
        $request->setQuery(new Parameters($query));

        /** @var RouteInterface $router */
        $router     = $this->getServiceLocator()->get('Router');
        $routeMatch = $router->match($request);

        if (!$routeMatch || !$routeMatch->getMatchedRouteName()) {
            return null;
        }

        $internalRoute = InternalRoute::fromRoute($routeMatch);
        $internalRoute->setOptions(['query' => $query]);

        return $internalRoute;
    }

    /**
     * Retorna o valor do parametro query "continue" da URL no HTTP_REFER, caso exista.
     *
     * @return String
     *      Retorna um {@link InternalRoute} já criptografado.
     */
    public function getContinueRouteFromReferer()
    {
        $continue = null;

        if ($referer = $this->getController()->urls()->getReferer()) {
            if ($parse_url = parse_url($referer, PHP_URL_QUERY)) {
                $query = [];
                parse_str($parse_url, $query);
                if (isset($query['continue'])) {
                    $continue = $query['continue'];
                }
            }
        }

        return $continue;
    }
} 
