<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetDashboardResults extends SaloonRequest
{
    protected ?string $dashboardId;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $dashboardId)
    {
        $this->dashboardId = $dashboardId;
    }

    public function defineEndpoint(): string
    {
        return "/analytics/dashboards/{$this->dashboardId}";
    }
}