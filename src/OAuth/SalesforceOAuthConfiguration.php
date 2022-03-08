<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

class SalesforceOAuthConfiguration
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $baseUrl;

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function toArray() : array
    {
        return [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectUri,
            'baseUrl' => $this->baseUrl
        ];
    }
}