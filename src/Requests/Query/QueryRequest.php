<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class QueryRequest extends Request
{
    protected Method $method = Method::GET;
    protected string $soqlQuery;

    public function __construct(string $query)
    {
        $this->soqlQuery = $query;
    }

    public function resolveEndpoint(): string
    {
        return '/query';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => $this->soqlQuery,
        ];
    }
}
