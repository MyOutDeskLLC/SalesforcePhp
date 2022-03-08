<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class UpdateRecords extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $method = Saloon::PATCH;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return 'composite/sobjects';
    }
}
