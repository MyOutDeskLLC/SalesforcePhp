<?php

namespace myoutdeskllc\SalesforcePhp\Connectors;

use myoutdeskllc\SalesforcePhp\Plugins\WithSalesforceAuthHeader;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\Plugins\AcceptsJson;
use Sammyjo20\Saloon\Traits\Plugins\AlwaysThrowsOnErrors;

class SalesforceConnector extends SaloonConnector
{
    use AcceptsJson;
    use WithSalesforceAuthHeader;
    use AlwaysThrowsOnErrors;

    public function defineBaseUrl(): string
    {
        return SalesforceApi::instanceUrl();
    }

    public function defaultHeaders(): array
    {
        return [];
    }
}
