<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexClass extends Request
{
    protected ?string $apexClassId = null;
    protected Method $method = Method::GET;

    public function __construct(string $apexClassId)
    {
        $this->apexClassId = $apexClassId;
    }

    public function resolveEndpoint(): string
    {
        return "/tooling/sobjects/ApexClass/{$this->apexClassId}";
    }
}
