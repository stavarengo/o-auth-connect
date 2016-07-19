<?php
/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\OAuthService;

interface OAuthServiceInterface
{
    /**
     * @return mixed
     */
    public function getServiceClient();
    
    /**
     * Verifica todas as dependencias para este serviço funcionar estão instaladas.
     *
     * @return true|string[]
     *      Retorna true se está tudo ok, ou um string[] com a lista das dependencias que estão faltando.
     */
    public function checkDependencies();

    /**
     * Retorna todos os scopoos que podemos pedir.
     * Qualquer scopo alem dos listados aqui não devem ser pedidos pois podem ser recusados pelo serviço de OAuth.
     *
     * @return string[]
     */
    public function getAllScopesWeCanAsk();

    /**
     * Scopos que sempre devem ser pedidos.
     *
     * @return string[]
     */
    public function getMinimumScopes();

    /**
     * Scopos que o usuário sempre deve autorizar caso contrário não vamos aceitar o token de autorização dele.
     *
     * @return string[]
     */
    public function getRequiredScopes();

    /**
     * URL do serviço OAuth para onde o usuário deve ser levado para fazer a autorização.
     * Normalmente nesta URL o serviço de OAuth mostra os destalhes de quem está pedindo autorização, os escopos que
     * estão sendo pedidos e por fim, dois botões: um para o usuário autorizar e outro para ele negar.
     *
     * @param $callbackUrl
     *      Para onde o serviço de OAuth vai redirecionar o usuário após ele responder se autoriza ou não.
     * @param $scopes
     *      Os scopos que estão sendo pedidos.
     *
     * @return string
     */
    /**
     *
     * @return mixed
     */
    public function getUrlToAskAuthorization($callbackUrl, $scopes);

    /**
     * @param $scopes
     *
     * @return AuthorizationResult
     */
    public function isAuthorized($scopes);
}
