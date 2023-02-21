<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetJobResults extends Request
{
    protected string $id;
    protected string $type;
    protected Method $method = Method::GET;

    public function __construct(string $id, string $resultType)
    {
        $this->id = $id;
        if ($resultType === BulkApiOptions::SUCCESSFUL_RESULTS) {
            $this->type = BulkApiOptions::SUCCESSFUL_RESULTS;
        } else {
            $this->type = BulkApiOptions::UNSUCCESSFUL_RESULTS;
        }
    }

    public function resolveEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}/".$this->type === BulkApiOptions::SUCCESSFUL_RESULTS ? BulkApiOptions::SUCCESSFUL_RESULTS : BulkApiOptions::UNSUCCESSFUL_RESULTS;
    }
}
