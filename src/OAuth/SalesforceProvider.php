<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class SalesforceProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected string $domain;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->domain = $options['baseUrl'] ?? 'https://test.salesforce.com';
        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl() : string
    {
        return $this->domain.'/services/oauth2/authorize';
    }

    public function getBaseAccessTokenUrl(array $params) : string
    {
        return $this->domain.'/services/oauth2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->domain . '/services/oauth2/userinfo';
    }

    protected function getDefaultScopes()
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            throw new IdentityProviderException($data[0]['message'] ?? $response->getReasonPhrase(), $statusCode, $response->getBody()->getContents());
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new SalesforceResourceOwner($response);
    }
}