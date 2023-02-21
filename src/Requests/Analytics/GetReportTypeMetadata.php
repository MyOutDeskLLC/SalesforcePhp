<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetReportTypeMetadata extends Request
{
    protected ?string $type = null;
    protected Method $method = Method::GET;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/reportTypes/{$this->type}";
    }
}
