<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateJobState extends Request implements HasBody
{
    use HasJsonBody;

    protected string $id;
    protected string $state = BulkApiOptions::UPLOAD_COMPLETE;
    protected Method $method = Method::PATCH;

    public function __construct(string $id, string $newJobState)
    {
        $this->id = $id;
        $this->state = $newJobState;
    }

    public function defaultBody(): array
    {
        return [
            'state' => $this->state,
        ];
    }

    public function resolveEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}";
    }
}
