<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class UpdateEmailTemplate extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $templateId = null;
    protected ?string $method = Saloon::PATCH;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $templateId)
    {
        $this->templateId = $templateId;
    }

    public function defineEndpoint(): string
    {
        return "/tooling/sobjects/EmailTemplate/{$this->templateId}";
    }
}