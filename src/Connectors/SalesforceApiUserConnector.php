<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\Traits\HasApiVersion;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class SalesforceApiUserConnector extends Connector
{
    use HasApiVersion;

    public function __construct(string $token)
    {
        $this->authenticator = new TokenAuthenticator($token);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://test.salesforce.com';
    }
}
