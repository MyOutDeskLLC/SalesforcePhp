<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration;
use myoutdeskllc\SalesforcePhp\Requests\OAuth\GetAccessTokenWithPKCERequest;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use Saloon\Contracts\OAuthAuthenticator;
use Saloon\Contracts\Response;
use Saloon\Exceptions\InvalidStateException;
use Saloon\Http\Connector;
use Saloon\Http\OAuth2\GetAccessTokenRequest;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SalesforceOAuthLoginConnector extends Connector
{
    use AuthorizationCodeGrant;
    protected string $codeVerifier = '';

    public function setOauthConfiguration(OAuthConfiguration $configuration, string $codeVerifier = ''): void
    {
        $this->oauthConfig()->setClientId($configuration->getClientId());
        $this->oauthConfig()->setClientSecret($configuration->getClientSecret());
        $this->oauthConfig()->setRedirectUri($configuration->getRedirectUri());
        $this->oauthConfig()->setAuthorizeEndpoint($this->resolveBaseUrl().'/services/oauth2/authorize');
        $this->oauthConfig()->setTokenEndpoint($this->resolveBaseUrl().'/services/oauth2/token');
        $this->codeVerifier = $codeVerifier;
    }

    public function getAccessToken(string $code, ?string $state = null, ?string $expectedState = null, bool $returnResponse = false): OAuthAuthenticator|Response
    {
        if (empty($this->codeVerifier)) {
            return $this->authorizeWithoutPkce($code, $state, $expectedState, $returnResponse);
        }

        $this->oauthConfig()->validate();

        if (!empty($state) && !empty($expectedState) && $state !== $expectedState) {
            throw new InvalidStateException();
        }

        $oauthPKCERequest = new GetAccessTokenWithPKCERequest($code, $this->oauthConfig());
        $defaultBody = $oauthPKCERequest->defaultBody();
        $defaultBody['code_verifier'] = $this->codeVerifier;

        $oauthPKCERequest->body()->set($defaultBody);
        $response = $this->send($oauthPKCERequest);

        if ($returnResponse === true) {
            return $response;
        }

        $response->throw();

        return $this->createOAuthAuthenticatorFromResponse($response, '');
    }

    public function authorizeWithoutPkce(string $code, ?string $state = null, ?string $expectedState = null, bool $returnResponse = false): OAuthAuthenticator|Response
    {
        $this->oauthConfig()->validate();

        if (!empty($state) && !empty($expectedState) && $state !== $expectedState) {
            throw new InvalidStateException();
        }

        $response = $this->send(new GetAccessTokenRequest($code, $this->oauthConfig()));

        if ($returnResponse === true) {
            return $response;
        }

        $response->throw();

        return $this->createOAuthAuthenticatorFromResponse($response);
    }

    public function resolveBaseUrl(): string
    {
        return SalesforceApi::getInstanceUrl();
    }
}
