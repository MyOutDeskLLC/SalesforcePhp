<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;


use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexPage extends Request
{
    protected ?string $pageId = null;
    protected Method $method = Method::GET;

    public function __construct(string $pageId)
    {
        $this->pageId = $pageId;
    }

    public function resolveEndpoint(): string
    {
        return "/tooling/sobjects/ApexPage/{$this->pageId}";
    }
}
