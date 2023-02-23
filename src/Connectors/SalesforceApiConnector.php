<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Traits\HasApiVersion;
use Saloon\Http\Connector;

class SalesforceApiConnector extends Connector
{
    use HasApiVersion;

    public function resolveBaseUrl(): string
    {
        return SalesforceApi::getInstanceUrl().'/services/data/'.SalesforceApi::getApiVersion();
    }
}
