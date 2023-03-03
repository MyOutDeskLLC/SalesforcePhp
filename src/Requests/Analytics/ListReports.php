<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListReports extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/analytics/reports';
    }
}
