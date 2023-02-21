<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DescribeGlobal extends Request
{
    protected Method $method = Method::GET;


    public function resolveEndpoint(): string
    {
        return 'sobjects';
    }
}
