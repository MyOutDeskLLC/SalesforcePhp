<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexLog extends SaloonRequest
{
    protected ?string $logId;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $logId)
    {
        $this->logId = $logId;
    }

    public function defineEndpoint(): string
    {
        return "/sobjects/ApexLog/{$this->logId}/Body";
    }
}