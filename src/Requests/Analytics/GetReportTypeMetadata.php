<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetReportTypeMetadata extends SaloonRequest
{
    protected ?string $type = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function defineEndpoint(): string
    {
        return "/analytics/reportTypes/{$this->type}";
    }
}