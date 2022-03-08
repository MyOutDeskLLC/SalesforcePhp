<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetReportMetadata extends SaloonRequest
{
    protected ?string $reportId;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $reportId)
    {
        $this->reportId = $reportId;
    }

    public function defineEndpoint(): string
    {
        return "/analytics/reports/{$this->reportId}/describe";
    }
}
