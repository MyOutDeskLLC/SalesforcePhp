<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class CreateEmailTemplate extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $method = Saloon::POST;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return '/tooling/sobjects/EmailTemplate';
    }
}
