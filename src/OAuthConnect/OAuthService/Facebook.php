<?php
namespace Sta\OAuthConnect\OAuthService;
/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
class Facebook implements OAuthServiceInterface
{
    /**
     * @var string
     */
    protected $appId;
    /**
     * @var string
     */
    protected $appSecret;

    /**
     * Facebook constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
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
        $helper   = $this->getFacebook()->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($callbackUrl, $scopes);

        return $loginUrl;
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
        if (!extension_loaded('mbstring')) {
            $dependencies[] = 'Module mbstring: http://php.net/manual/en/book.mbstring.php';
        }

        if (!class_exists('\Facebook\Facebook')) {
            $dependencies[] = 'Facebook SDK version 5.2.0 or great: https://developers.facebook.com/docs/reference/php';
        } else {
            $minVersion = '5.2.1';
            if (!version_compare($minVersion, \Facebook\Facebook::VERSION, '>=')) {
                $dependencies[] = 'Facebook SDK must be version 5.2.0 or great.';
            }
        }

        return ($dependencies ? $dependencies : true);
    }

    private function getFacebook()
    {
        $fb = new \Facebook\Facebook(
            [
                'app_id' => $this->appId,
                'app_secret' => $this->appSecret,
                'default_graph_version' => 'v2.5',
            ]
        );

        return $fb;
    }

    public function isAuthorized($scopes)
    {
        $fb          = $this->getFacebook();
        $helper      = $fb->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();

        if (!$accessToken || $accessToken->isExpired()) {
            return false;
        }

        $accessTokenMetadata = $fb->getOAuth2Client()->debugToken($accessToken);
        $authorizedScopes    = $accessTokenMetadata->getScopes();
        $scopes              = array_unique($scopes);
        $missedScopes        = array_diff($scopes, $authorizedScopes);


        $result = new IsAuthorizedResult();
        $result->setAuthorizedScopes($authorizedScopes);
        $result->setMissedScopes($missedScopes);
        $result->setAuthorized(!$missedScopes);

        return $result;
    }
}
