<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetAsyncReportResult extends Request
{
    protected ?string $id = null;
    protected ?string $queueId = null;
    protected Method $method = Method::GET;

    public function __construct(string $originalReportId, string $queueId)
    {
        $this->id = $originalReportId;
        $this->queueId = $queueId;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/reports/{$this->id}/instances/{$this->queueId}";
    }
}
