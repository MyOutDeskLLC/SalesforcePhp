<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Organization;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetLimits extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return 'limits';
    }
}
