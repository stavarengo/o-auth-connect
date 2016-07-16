<?php
/**
 * webapp Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\OAuthService;

use Sta\Commons\StdClass;

class AuthorizationResult extends StdClass
{
    /**
     * @var string[]
     */
    protected $authorizedScopes = [];

    /**
     * @var string[]
     */
    protected $missedScopes = [];

    /**
     * @var string
     */
    protected $accessToken;
    /**
     * @var string[]
     */
    protected $requiredScopes;

    /**
     * AuthorizationResult constructor.
     *
     * @param \string[] $requiredScopes
     * @param \string[] $authorizedScopes
     * @param string $accessToken
     */
    public function __construct($accessToken = null, array $requiredScopes = [], array $authorizedScopes = [])
    {
        $this->requiredScopes   = $requiredScopes;
        $this->authorizedScopes = $authorizedScopes;
        $this->accessToken      = $accessToken;
        
        parent::__construct([]);
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->accessToken && !$this->missedScopes;
    }

    /**
     * @return string[]
     */
    public function getAuthorizedScopes()
    {
        return $this->authorizedScopes;
    }

    /**
     * @param string[] $authorizedScopes
     *
     * @return $this
     */
    public function setAuthorizedScopes(array $authorizedScopes)
    {
        $this->authorizedScopes = array_unique($authorizedScopes);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMissedScopes()
    {
        return array_diff($this->getRequiredScopes(), $this->getAuthorizedScopes());
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     *
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRequiredScopes()
    {
        return $this->requiredScopes;
    }

    /**
     * @param string[] $requiredScopes
     *
     * @return $this
     */
    public function setRequiredScopes(array $requiredScopes)
    {
        $this->requiredScopes = array_unique($requiredScopes);

        return $this;
    }

}
