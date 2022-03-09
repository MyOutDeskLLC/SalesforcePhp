<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class ExecuteAnonymousApex extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return "/tooling/executeAnonymous/";
    }

    public function defaultQuery(): array
    {
        return [
            'anonymousBody' => "System.debug('hello from myoutdesk!');"
        ];
    }
}