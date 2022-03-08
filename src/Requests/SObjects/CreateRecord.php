<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class CreateRecord extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $object = '';
    protected ?string $method = Saloon::POST;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $object)
    {
        $this->object = $object;
    }

    public function defineEndpoint(): string
    {
        return "sobjects/{$this->object}";
    }
}