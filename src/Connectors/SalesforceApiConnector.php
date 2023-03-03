<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\SalesforceApi;
use Saloon\Http\Connector;

class SalesforceApiConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return SalesforceApi::getInstanceUrl().'/services/data/'.SalesforceApi::getApiVersion();
    }
}
