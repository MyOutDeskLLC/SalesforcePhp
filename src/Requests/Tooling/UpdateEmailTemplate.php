<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateEmailTemplate extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $templateId = null;
    protected Method $method = Method::PATCH;

    public function __construct(string $templateId)
    {
        $this->templateId = $templateId;
    }

    public function resolveEndpoint(): string
    {
        return "/tooling/sobjects/EmailTemplate/{$this->templateId}";
    }
}
