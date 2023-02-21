<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class RunReport extends Request implements HasBody
{
    use HasJsonBody;

    protected string $id;
    protected Method $method = Method::POST;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/reports/{$this->id}";
    }
}
