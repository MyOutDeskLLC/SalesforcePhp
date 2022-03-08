<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class UpdateRecord extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $object = '';
    protected ?string $id = '';
    protected ?string $method = Saloon::PATCH;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $object, string $id)
    {
        $this->object = $object;
        $this->id = $id;
    }

    public function defineEndpoint(): string
    {
        return "sobjects/{$this->object}/{$this->id}";
    }
}