<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;


use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetRecords extends Request
{
    protected ?string $object;
    protected Method $method = Method::GET;


    public function __construct(string $object)
    {
        $this->object = $object;
    }

    public function resolveEndpoint(): string
    {
        return "composite/sobjects/{$this->object}";
    }
}
