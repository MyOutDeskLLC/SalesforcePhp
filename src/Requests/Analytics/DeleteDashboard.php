<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class DeleteDashboard extends SaloonRequest
{
    protected ?string $dashboardId = null;
    protected ?string $method = Saloon::DELETE;
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