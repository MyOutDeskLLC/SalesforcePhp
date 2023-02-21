<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class CloneDashboard extends Request
{
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
