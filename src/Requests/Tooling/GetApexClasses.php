<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexClasses extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;


    public function defineEndpoint(): string
    {
        return "/tooling/apexManifest";
    }
}