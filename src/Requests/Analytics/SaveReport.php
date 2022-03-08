<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class SaveReport extends SaloonRequest
{
    protected ?string $id;
    protected ?string $method = Saloon::PATCH;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function defineEndpoint(): string
    {
        return "/analytics/reports/{$this->id}";
    }
}