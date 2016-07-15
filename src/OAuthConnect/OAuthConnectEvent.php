<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Sta\OAuthConnect;

use Sta\OAuthConnect\OAuthService\IsAuthorizedResult;
use Zend\EventManager\Event;

class OAuthConnectEvent extends Event
{
    const EVENT_OAUTH_RESPONSE = 'oauth_response';

    /**
     * @var IsAuthorizedResult
     */
    protected $authorizedResult;

    /**
     * OAuthConnectEvent constructor.
     *
     * @param \Zend\Mvc\MvcEvent $mvcEvent
     * @param \Sta\OAuthConnect\OAuthService\IsAuthorizedResult $authorizedResult
     */
    public function __construct(\Sta\OAuthConnect\OAuthService\IsAuthorizedResult $authorizedResult)
    {
        $this->authorizedResult = $authorizedResult;
        parent::__construct();
    }

    /**
     * @return \Sta\OAuthConnect\OAuthService\IsAuthorizedResult
     */
    public function getAuthorizedResult()
    {
        return $this->authorizedResult;
    }

    /**
     * @param \Sta\OAuthConnect\OAuthService\IsAuthorizedResult $authorizedResult
     *
     * @return $this
     */
    public function setAuthorizedResult($authorizedResult)
    {
        $this->authorizedResult = $authorizedResult;

        return $this;
    }

}
