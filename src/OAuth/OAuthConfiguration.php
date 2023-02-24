<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

class OAuthConfiguration
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;


    public static function withOauthConfiguration(array $oAuthConfiguration): self
    {
        $oauthConfig = new self();
        $oauthConfig->setClientId($oAuthConfiguration['client_id']);
        $oauthConfig->setClientSecret($oAuthConfiguration['client_secret']);
        $oauthConfig->setRedirectUri($oAuthConfiguration['redirect_uri']);

        return $oauthConfig;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }
}
