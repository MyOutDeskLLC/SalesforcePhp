<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetRecord extends Request
{
    protected ?string $recordId;
    protected ?string $object;
    protected Method $method = Method::GET;

    public function __construct(string $object, string $recordId)
    {
        $this->recordId = $recordId;
        $this->object = $object;
    }

    public function resolveEndpoint(): string
    {
        return "sobjects/{$this->object}/{$this->recordId}";
    }
}
