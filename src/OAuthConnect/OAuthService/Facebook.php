<?php
namespace Sta\OAuthConnect\OAuthService;
/**
 * front-end Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
class Facebook
{
    /**
     * Escopos do facebook que precisão ser autorizados para o usuário poder  entrar no site.
     * @var array
     */
    public static $FACEBOOK_SCOPE_REQUIRED = array(
        'user_friends',
    );

    /**
     * Retorna todos os scopoos que podemos pedir. 
     * Qualquer scopo alem dos listados aqui não devem ser pedidos pois podem ser recusados pelo serviço de OAuth.
     */
    public function getAllScopesWeCanAsk()
    {
        
    }
    
    /**
     * Scopos que sempre devem ser pedidos.
     */
    public function getMinimumScopes()
    {
        
    }

    /**
     * Scopos que o usuário sempre deve autorizar caso contrário não vamos aceitar o token de autorização dele.
     */
    public function getRequiredScopes()
    {
        
    }
}
