<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteRecords extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return "composite/sobjects";
    }
}
