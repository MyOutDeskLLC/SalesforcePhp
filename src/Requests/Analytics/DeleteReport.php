<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;


class DeleteReport extends Request
{
    protected ?string $id = null;
    protected Method $method = Method::DELETE;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveEndpoint(): string
    {
        return "/analytics/reports/{$this->id}";
    }
}
