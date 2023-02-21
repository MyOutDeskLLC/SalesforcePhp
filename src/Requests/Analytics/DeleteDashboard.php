<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteDashboard extends Request
{
    protected ?string $dashboardId = null;
    protected Method $method = Method::DELETE;

    public function __construct(string $dashboardId)
    {
        $this->dashboardId = $dashboardId;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/dashboards/{$this->dashboardId}";
    }
}
