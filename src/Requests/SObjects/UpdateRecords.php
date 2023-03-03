<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateRecords extends Request
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function resolveEndpoint(): string
    {
        return 'composite/sobjects';
    }
}
