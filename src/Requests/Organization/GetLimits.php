<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Organization;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetLimits extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return 'limits';
    }
}
