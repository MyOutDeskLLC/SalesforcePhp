<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class SalesforceAuthenticator
{
    protected SalesforceProvider $provider;
    protected AccessToken $token;

    public function getProvider() : SalesforceProvider
    {
        return $this->provider;
    }

    public function configure(SalesforceOAuthConfiguration $configuration) : void
    {
        $this->provider = new SalesforceProvider($configuration->toArray());
    }

    public function getAuthorizationUrl() : string
    {
        return $this->provider->getAuthorizationUrl();
    }

    public function refreshTokenIfNeeded(AccessTokenInterface $token, callable $callback) : void
    {
        if(!$token->hasExpired()) {
            return;
        }
        $token = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $token->getRefreshToken()
        ]);
        $this->token = $token;

        $callback($token);
    }

    public function handleAuthorizationCallback(string $code) : AccessToken
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);
    }

    public function getResourceOwner(AccessToken $token): ResourceOwnerInterface
    {
        return $this->provider->getResourceOwner($token);
    }
}