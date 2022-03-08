<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetRecord extends SaloonRequest
{
    protected ?string $recordId;
    protected ?string $object;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $object, string $recordId)
    {
        $this->recordId = $recordId;
        $this->object = $object;
    }

    public function defineEndpoint(): string
    {
        return "sobjects/{$this->object}/{$this->recordId}";
    }
}