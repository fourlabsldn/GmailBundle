<?php

namespace FL\GmailBundle\Token;

/**
 * Class AccessToken.
 */
class AccessToken
{
    /**
     * @var array
     */
    private $jsonToken = null;

    /**
     * Get the current access token as a json string.
     *
     * @return string|null
     */
    public function getJsonToken()
    {
        return $this->jsonToken;
    }

    /**
     * @param string $jsonToken
     *
     * @return AccessToken
     */
    public function setJsonToken(string $jsonToken)
    {
        $this->jsonToken = $jsonToken;

        return $this;
    }
}
