<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateDashboard extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $dashboardId;
    protected ?string $targetFolder;
    protected Method $method = Method::PATCH;

    public function __construct(string $dashboardId)
    {
        $this->dashboardId = $dashboardId;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/dashboards/{$this->dashboardId}";
    }
}
