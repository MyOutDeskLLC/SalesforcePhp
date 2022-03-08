<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;

class GetJob extends \Sammyjo20\Saloon\Http\SaloonRequest
{
    protected ?string $id = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function defineEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}";
    }
}
