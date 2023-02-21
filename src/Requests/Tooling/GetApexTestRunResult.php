<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexTestRunResult extends Request
{
    protected ?string $testRunId = null;
    protected Method $method = Method::GET;

    public function __construct(string $testRunId)
    {
        $this->testRunId = $testRunId;
    }

    public function resolveEndpoint(): string
    {
        return "/tooling/sobjects/ApexTestRunResult/{$this->testRunId}";
    }
}
