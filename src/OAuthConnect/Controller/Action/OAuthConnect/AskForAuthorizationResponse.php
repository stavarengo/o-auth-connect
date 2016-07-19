<?php
/**
 * irmo Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\Controller\Action\OAuthConnect;

use App\Auth\OAuthConnect\ApiProblem;
use Sta\Commons\CryptQueryParam;
use Sta\Commons\InternalRoute;
use Sta\OAuthConnect\Controller\AbstractActionExController;
use Sta\OAuthConnect\Controller\Action\AbstractAction;
use Sta\OAuthConnect\OAuthConnectEvent;
use Sta\OAuthConnect\OAuthService\AuthorizationResult;
use Sta\OAuthConnect\OAuthService\OAuthServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\PhpEnvironment\Response;
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
    const OPT_ASYNC_AUTHORIZATION = 'sta-async';
    const SESSION_DATA_TO_USE_AFTER_RESPONSE = self::class . '::SESSION_DATA_TO_USE_AFTER_RESPONSE';

    /**
     * @var EventManagerInterface
     */
    protected $events;
    /**
     * @var array
     */
    protected $dataToUseAfterResponse;

    /**
     * AskForAuthorizationResponse constructor.
     *
     * @param \Zend\EventManager\EventManagerInterface $events
     */
    public function __construct(\Zend\EventManager\EventManagerInterface $events)
    {
        $this->events = $events;
    }

    /**
     * Executa a ação.
     *
     * @return mixin
     */
    public function execute()
    {
        $dataToUseAfterResponse = $this->_getDataToUseAfterResponse();
        if ($dataToUseAfterResponse === null) {
            return $this->error('Invalid session state. First you need to access the route "sta/oAuthConnect/ask".');
        }

        /** @var OAuthServiceInterface $oAuthService */
        $oAuthService = $this->getController()->getServiceLocator()->get(
            'Sta\OAuthConnect\OAuthService\\' . $dataToUseAfterResponse['oAuthServiceName']
        );

        $requestedScopes = $dataToUseAfterResponse['scopes'];

        $authorizationResult = $oAuthService->isAuthorized($requestedScopes);
        if (!($authorizationResult instanceof AuthorizationResult)) {
            return $this->error(
                'This OAuth Service implementation method ' . get_class($oAuthService) . '::isAuthorized() must ' .
                'return a instance of ' . \Sta\OAuthConnect\OAuthService\AuthorizationResult::class . '.'
            );
        }

        $oAuthConnectEvent = new OAuthConnectEvent($authorizationResult, $oAuthService);
        $eventResponses    = $this->events->trigger(
            OAuthConnectEvent::EVENT_OAUTH_RESPONSE,
            $oAuthConnectEvent,
            function ($r) {
                return $r instanceof Response;
            }
        );

        $eventResult = $eventResponses->last();
        if ($eventResult instanceof Response) {
            return $eventResult;
        }

        $routeToRedirect = $dataToUseAfterResponse['continue'];
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

            if ($eventResult instanceof ApiProblem
                || (class_exists('\ZF\ApiProblem\ApiProblem') && $eventResult instanceof \ZF\ApiProblem\ApiProblem)
            ) {
                $viewModel->error = $eventResult->toArray();
            }

            return $viewModel;
        }
    }

    /**
     * @param $continueAfterResponse
     */
    public static function storeDateToUseAfterResponse($continueAfterResponse, $oAuthServiceName, array $scopes)
    {
        if ($continueAfterResponse == self::OPT_ASYNC_AUTHORIZATION) {
            $continueAfterResponse = null;
        }

        $_SESSION[self::SESSION_DATA_TO_USE_AFTER_RESPONSE] = [
            'continue' => $continueAfterResponse,
            'oAuthServiceName' => $oAuthServiceName,
            'scopes' => $scopes,
        ];
    }

    private function _getDataToUseAfterResponse()
    {
        if (!$this->dataToUseAfterResponse) {
            $data = null;
            if (isset($_SESSION[self::SESSION_DATA_TO_USE_AFTER_RESPONSE])) {
                $data = $_SESSION[self::SESSION_DATA_TO_USE_AFTER_RESPONSE];
                unset($_SESSION[self::SESSION_DATA_TO_USE_AFTER_RESPONSE]);
            }

            $this->dataToUseAfterResponse = $data;
        }

        return $this->dataToUseAfterResponse;
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
