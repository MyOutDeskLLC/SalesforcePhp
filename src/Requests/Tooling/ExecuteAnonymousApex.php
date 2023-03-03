<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ExecuteAnonymousApex extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/tooling/executeAnonymous/';
    }

    public function defaultQuery(): array
    {
        return [
            'anonymousBody' => "System.debug('hello from myoutdesk!');",
        ];
    }
}
