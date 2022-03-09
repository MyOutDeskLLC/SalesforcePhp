<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexTestRunResult extends SaloonRequest
{
    protected ?string $testRunId = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $testRunId)
    {
        $this->testRunId = $testRunId;
    }

    public function defineEndpoint(): string
    {
        return "/tooling/sobjects/ApexTestRunResult/{$this->testRunId}";
    }
}
