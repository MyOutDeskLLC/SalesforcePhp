<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;


use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateRecord extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $object = '';
    protected ?string $id = '';
    protected Method $method = Method::PATCH;


    public function __construct(string $object, string $id)
    {
        $this->object = $object;
        $this->id = $id;
    }

    public function resolveEndpoint(): string
    {
        return "sobjects/{$this->object}/{$this->id}";
    }
}
