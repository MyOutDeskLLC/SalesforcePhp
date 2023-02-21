<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\Traits\HasApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Http\Connector;
use Saloon\Traits\Body\HasFormBody;

class SalesforceApiUserLoginConnector extends Connector implements HasBody
{
    use HasFormBody;
    use HasApiVersion;

    protected bool $sandbox = true;

    public function resolveBaseUrl(): string
    {
        if($this->sandbox) {
            return 'https://test.salesforce.com';
        }

        return 'https://login.salesforce.com';
    }
}