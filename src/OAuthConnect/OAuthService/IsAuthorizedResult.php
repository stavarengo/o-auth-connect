<?php
/**
 * webapp Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace Sta\OAuthConnect\OAuthService;

use Sta\Commons\StdClass;

class IsAuthorizedResult extends StdClass
{
    /**
     * @var bool
     */
    protected $authorized;

    /**
     * @var array
     */
    protected $authorizedScopes;

    /**
     * @var array
     */
    protected $missedScopes;

    /**
     * @return bool
     */
    public function getAuthorized()
    {
        return $this->authorized;
    }

    /**
     * @param bool $authorized
     *
     * @return $this
     */
    public function setAuthorized($authorized)
    {
        $this->authorized = $authorized;

        return $this;
    }

    /**
     * @return array
     */
    public function getAuthorizedScopes()
    {
        return $this->authorizedScopes;
    }

    /**
     * @param array $authorizedScopes
     *
     * @return $this
     */
    public function setAuthorizedScopes(array $authorizedScopes)
    {
        $this->authorizedScopes = $authorizedScopes;

        return $this;
    }

    /**
     * @return array
     */
    public function getMissedScopes()
    {
        return $this->missedScopes;
    }

    /**
     * @param array $missedScopes
     *
     * @return $this
     */
    public function setMissedScopes(array $missedScopes)
    {
        $this->missedScopes = $missedScopes;

        return $this;
    }

}
