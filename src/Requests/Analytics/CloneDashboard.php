<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CloneDashboard extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $dashboardId;
    protected ?string $targetFolder;
    protected Method $method = Method::POST;

    public function __construct(string $dashboardId, string $newFolderId)
    {
        $this->dashboardId = $dashboardId;
        $this->targetFolder = $newFolderId;
    }

    public function resolveEndpoint(): string
    {
        return '/analytics/dashboards';
    }

    public function defaultBody(): array
    {
        return [
            'folderId' => $this->targetFolder,
        ];
    }

    public function defaultQuery(): array
    {
        return [
            'cloneId' => $this->dashboardId,
        ];
    }
}
