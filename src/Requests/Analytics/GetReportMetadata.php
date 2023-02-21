<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetReportMetadata extends Request
{
    protected ?string $reportId;
    protected Method $method = Method::GET;

    public function __construct(string $reportId)
    {
        $this->reportId = $reportId;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/reports/{$this->reportId}/describe";
    }
}
