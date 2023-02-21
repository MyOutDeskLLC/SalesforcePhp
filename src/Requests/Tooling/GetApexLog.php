<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;


use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexLog extends Request
{
    protected ?string $logId;
    protected Method $method = Method::GET;


    public function __construct(string $logId)
    {
        $this->logId = $logId;
    }

    public function resolveEndpoint(): string
    {
        return "/sobjects/ApexLog/{$this->logId}/Body";
    }
}
