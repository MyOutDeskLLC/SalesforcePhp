<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

class OAuthConfiguration
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $baseUri;

    public function construct(array $oAuthConfiguration)
    {
        $this->clientId = $oAuthConfiguration['client_id'];
        $this->clientSecret = $oAuthConfiguration['client_secret'];
        $this->redirectUri = $oAuthConfiguration['redirect_uri'];
        $this->baseUri = $oAuthConfiguration['base_uri'];
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

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }
}
