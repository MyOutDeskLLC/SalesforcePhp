<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Traits\HasApiVersion;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class SalesforceApiConnector extends Connector
{
    use HasApiVersion;
    protected ?string $instanceUrl;

    public function resolveBaseUrl(): string
    {
        return $this->instanceUrl.'/services/data/'.SalesforceApi::getApiVersion();
    }
}
