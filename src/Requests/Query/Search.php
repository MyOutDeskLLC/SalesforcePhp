<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class Search extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return 'parameterizedSearch';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => '',
        ];
    }
}
