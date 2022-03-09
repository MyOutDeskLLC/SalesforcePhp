<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexClass extends SaloonRequest
{
    protected ?string $apexClassId = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $apexClassId)
    {
        $this->apexClassId = $apexClassId;
    }

    public function defineEndpoint(): string
    {
        return "/tooling/sobjects/ApexClass/{$this->apexClassId}";
    }
}
