<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class QueueReport extends SaloonRequest
{
    protected ?string $id = null;
    protected ?string $method = Saloon::POST;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function defineEndpoint(): string
    {
        return "/analytics/reports/{$this->id}/instances";
    }
}