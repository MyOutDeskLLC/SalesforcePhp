<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDashboardMetadata extends Request
{
    protected ?string $dashboardId;
    protected Method $method = Method::GET;

    public function __construct(string $dashboardId)
    {
        $this->dashboardId = $dashboardId;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/dashboards/{$this->dashboardId}/describe";
    }
}
