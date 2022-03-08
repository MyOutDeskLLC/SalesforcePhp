<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class UpdateDashboard extends SaloonRequest
{
    protected ?string $dashboardId;
    protected ?string $targetFolder;
    protected ?string $method = Saloon::PATCH;
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