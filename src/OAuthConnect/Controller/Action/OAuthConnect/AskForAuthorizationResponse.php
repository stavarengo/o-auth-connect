<?php
/**
 * irmo Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\Controller\Action\OAuthConnect;

use Sta\Commons\CryptQueryParam;
use Sta\Commons\InternalRoute;
use Sta\OAuthConnect\Controller\AbstractActionExController;
use Sta\OAuthConnect\Controller\Action\AbstractAction;
use Sta\OAuthConnect\OAuthService\Facebook;
use Sta\OAuthConnect\OAuthService\OAuthServiceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Este callback:
 *  1) recebe a resposta do serviço de OAuth;
 *  2) atualiza ou cadastra os dados do usuário;
 *  3) faz login automático do usuário (se não tiver ninquem logado)
 *  3) redireciona para uma rota interna do Reminds.
 *
 * A rota para onde vamos redirecionar (3º passo) pode receber os seguintes parâmetros via GET:
 *  - Um parâemtro cujo nome é igual a {@link
 * \Web\Controller\Action\Facebook\AskForAuthorizationResponse::QUERY_SOCIAL_NAME}. Este parâmetro terá o nome da Rede
 * Social que foi feito a tentativa de autorização. Ex: facebook.
 *  - Um parâemtro cujo nome é igual a {@link
 * \Web\Controller\Action\Facebook\AskForAuthorizationResponse::QUERY_AUTHORIZED}. Este parametro será igual a "1" caso
 * o usuário tenha concordado em compartilhar os dados com o Reminds. Se o usuário não autorizou este parametro tera
 * qualquer coisa diferente de "1" ou até mesmo pode não ser setado na URL.
 *
 * @package Web\Controller\Action\Facebook
 */
class AskForAuthorizationResponse extends AbstractAction
{
    const QUERY_PARAM_REQUESTED_SCOPES = 'requestedScopes';
    const OPT_ASYNC_AUTHORIZATION = 'sta-async';
    const SESSION_PARAM_CONTINUE_AFTER_LOGIN = self::class . '::SESSION_PARAM_CONTINUE_AFTER_LOGIN';

    /**
     * @param $continueAfterResponse
     */
    public static function storeWhereToRedirectAfterResponse($continueAfterResponse)
    {
        if ($continueAfterResponse instanceof \Sta\Commons\InternalRoute) {
            $cryptQueryParam = new \Sta\Commons\CryptQueryParam();
            $continue        = $cryptQueryParam->crypt($continueAfterResponse->toArray());
        } else {
            $continue = $continueAfterResponse;
        }
        if ($continue) {
            $_SESSION[self::SESSION_PARAM_CONTINUE_AFTER_LOGIN] = $continue;
        }
    }

    /**
     * Executa a ação.
     *
     * @return mixin
     */
    public function execute()
    {
        $oAuthService = null;
        if ($this->params()->fromQuery('oAuhtService') == md5(Facebook::class)) {
            /** @var OAuthServiceInterface $oAuthService */
            $oAuthService = $this->getController()->getServiceLocator()->get(
                'Sta\OAuthConnect\OAuthService\Facebook'
            );
        }

        if (!$oAuthService) {
            die('implementar o que acontece aqui');
        }

        $requestedScopes = explode(
            ',',
            base64_decode($this->params()->fromQuery(self::QUERY_PARAM_REQUESTED_SCOPES))
        );

        $isAuthorizedResult = $oAuthService->isAuthorized($requestedScopes);
        if ($isAuthorizedResult) {
            // disparar um evento aqui
        } else {
            // disparar um evento aqui
        }

        $routeToRedirect = $this->_getInternalRedirectRoute();
        if ($routeToRedirect) {
            return $this->getController()->redirect()->toRoute(
                $routeToRedirect->getRoute(),
                $routeToRedirect->getParams(),
                $routeToRedirect->getOptions()
            );
        } else {
            // foi uma autorizacao asincrona
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);

            return $viewModel;
        }
    }

    /**
     * Retorna a rota de redirecionamento que está criptografada na URL da requisição.
     *
     * @return InternalRoute
     *      Retorna null se é uma authoizacao asincrona
     */
    private function _getInternalRedirectRoute()
    {
        $continueRoute = null;

        if (isset($_SESSION[self::SESSION_PARAM_CONTINUE_AFTER_LOGIN])) {
            $continueValue = $_SESSION[self::SESSION_PARAM_CONTINUE_AFTER_LOGIN];
            unset($_SESSION[self::SESSION_PARAM_CONTINUE_AFTER_LOGIN]);
            if ($continueValue == self::OPT_ASYNC_AUTHORIZATION) {
                $continueRoute = null;
            } else {
                $continueRoute = new InternalRoute(CryptQueryParam::decrypt_($continueValue));
            }
        }

        return $continueRoute;
    }

    /**
     * Retorna a URL para onde a Rede Social vai redirecionar a resposta do usuário.
     * Será uma URL que aponta para está mesma action. Por sua vês está action vai tratar a resposta e redirecionar o
     * navegador para a rota $internalRoute
     *
     * @param ServiceLocatorInterface $sl
     *
     * @param \Sta\Commons\InternalRoute|string $continueAfterResponse
     *      Rota para onde iremos redirecionar o usuário apos receber a resposta da rede social.
     *      Use {@link \Web\Controller\Action\Facebook\AskForAuthorizationResponse::OPT_ASYNC_AUTHORIZATION } quando
     *      for um authorization asyncrona
     *
     * @return string
     */
    public static function getCallbackUrl(
        AbstractActionExController $controller, OAuthServiceInterface $oAuhtService, array $scopes = []
    ) {
        if ($scopes) {
            $query[self::QUERY_PARAM_REQUESTED_SCOPES] = base64_encode(implode(',', $scopes));
        }

        $str = $controller->url()->fromRoute(
            'sta/oAuthConnect/response',
            [],
            [
                'force_canonical' => true,
                'query' => [
                    'oAuhtService' => md5(get_class($oAuhtService)),
                ],
            ]
        );

        return $str;
    }

}
