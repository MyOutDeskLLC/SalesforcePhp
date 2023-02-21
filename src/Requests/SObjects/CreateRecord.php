<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateRecord extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $object = '';
    protected Method $method = Method::POST;

    public function __construct(string $object)
    {
        $this->object = $object;
    }

    public function resolveEndpoint(): string
    {
        return "sobjects/{$this->object}";
    }
}
