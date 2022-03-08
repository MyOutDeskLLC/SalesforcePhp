<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class CloneDashboard extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $dashboardId;
    protected ?string $targetFolder;
    protected ?string $method = Saloon::POST;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $dashboardId, string $newFolderId)
    {
        $this->dashboardId = $dashboardId;
        $this->targetFolder = $newFolderId;
    }

    public function defineEndpoint(): string
    {
        return '/analytics/dashboards';
    }

    public function defaultData(): array
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
