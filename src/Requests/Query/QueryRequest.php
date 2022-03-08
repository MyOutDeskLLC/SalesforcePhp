<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class QueryRequest extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    protected string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function defineEndpoint(): string
    {
        return '/query';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => $this->query,
        ];
    }
}
