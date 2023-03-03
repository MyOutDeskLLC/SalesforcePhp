<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetJob extends Request
{
    protected string $id;
    protected Method $method = Method::GET;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}";
    }
}
