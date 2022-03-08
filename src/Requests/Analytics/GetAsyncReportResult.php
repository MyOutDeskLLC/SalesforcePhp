<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetAsyncReportResult extends SaloonRequest
{
    protected ?string $id = null;
    protected ?string $queueId = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $originalReportId, string $queueId)
    {
        $this->id = $originalReportId;
        $this->queueId = $queueId;
    }

    public function defineEndpoint(): string
    {
        return "/analytics/reports/{$this->id}/instances/{$this->queueId}";
    }
}
