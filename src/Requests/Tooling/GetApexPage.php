<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexPage extends SaloonRequest
{
    protected ?string $pageId = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $pageId)
    {
        $this->pageId = $pageId;
    }

    public function defineEndpoint(): string
    {
        return "/tooling/sobjects/ApexPage/{$this->pageId}";
    }
}