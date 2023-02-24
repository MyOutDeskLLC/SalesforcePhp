<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetDashboardResults extends Request
{
    protected ?string $dashboardId;
    protected Method $method = Method::GET;

    public function __construct(string $dashboardId)
    {
        $this->dashboardId = $dashboardId;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/dashboards/{$this->dashboardId}";
    }
}
