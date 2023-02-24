<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration;
use myoutdeskllc\SalesforcePhp\Traits\HasApiVersion;
use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SalesforceOAuthLoginConnector extends Connector
{
    use AuthorizationCodeGrant;
    use HasApiVersion;

    public function setOauthConfiguration(OAuthConfiguration $configuration): void
    {
        $this->oauthConfig()->setClientId($configuration->getClientId());
        $this->oauthConfig()->setClientSecret($configuration->getClientSecret());
        $this->oauthConfig()->setRedirectUri($configuration->getRedirectUri());
        $this->oauthConfig()->setAuthorizeEndpoint($this->resolveBaseUrl().'/services/oauth2/authorize');
        $this->oauthConfig()->setTokenEndpoint($this->resolveBaseUrl().'/services/oauth2/token');
    }

    public function resolveBaseUrl(): string
    {
        if ($this->sandbox) {
            return 'https://test.salesforce.com';
        }

        return 'https://login.salesforce.com';
    }
}
