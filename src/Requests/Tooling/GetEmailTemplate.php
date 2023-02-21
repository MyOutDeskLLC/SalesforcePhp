<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetEmailTemplate extends Request
{
    protected ?string $templateId = null;
    protected Method $method = Method::GET;

    public function __construct(string $templateId)
    {
        $this->templateId = $templateId;
    }

    public function resolveEndpoint(): string
    {
        return "/tooling/sobjects/EmailTemplate/{$this->templateId}";
    }
}
