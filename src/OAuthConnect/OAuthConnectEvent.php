<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Sta\OAuthConnect;

use Sta\OAuthConnect\OAuthService\AuthorizationResult;
use Sta\OAuthConnect\OAuthService\OAuthServiceInterface;
use Zend\EventManager\Event;

class OAuthConnectEvent extends Event
{
    const EVENT_OAUTH_RESPONSE = 'oauth_response';

    /**
     * @var AuthorizationResult
     */
    protected $authorizationResult;
    /**
     * @var OAuthServiceInterface
     */
    protected $oAuthService;

    /**
     * OAuthConnectEvent constructor.
     *
     * @param \Sta\OAuthConnect\OAuthService\AuthorizationResult $authorizationResult
     * @param \Sta\OAuthConnect\OAuthService\OAuthServiceInterface $oAuthService
     */
    public function __construct(
        \Sta\OAuthConnect\OAuthService\AuthorizationResult $authorizationResult,
        \Sta\OAuthConnect\OAuthService\OAuthServiceInterface $oAuthService
    ) {
        $this->authorizationResult = $authorizationResult;
        $this->oAuthService        = $oAuthService;
        parent::__construct();
    }

    /**
     * @return \Sta\OAuthConnect\OAuthService\OAuthServiceInterface
     */
    public function getOAuthService()
    {
        return $this->oAuthService;
    }

    /**
     * @param \Sta\OAuthConnect\OAuthService\OAuthServiceInterface $oAuthService
     *
     * @return $this
     */
    public function setOAuthService($oAuthService)
    {
        $this->oAuthService = $oAuthService;

        return $this;
    }

    /**
     * @return \Sta\OAuthConnect\OAuthService\AuthorizationResult
     */
    public function getAuthorizationResult()
    {
        return $this->authorizationResult;
    }

    /**
     * @param \Sta\OAuthConnect\OAuthService\AuthorizationResult $authorizationResult
     *
     * @return $this
     */
    public function setAuthorizationResult($authorizationResult)
    {
        $this->authorizationResult = $authorizationResult;

        return $this;
    }

}
