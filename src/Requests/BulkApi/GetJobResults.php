<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use Sammyjo20\Saloon\Constants\Saloon;

class GetJobResults extends \Sammyjo20\Saloon\Http\SaloonRequest
{
    protected ?string $id = null;
    protected ?string $type = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $id, string $resultType)
    {
        $this->id = $id;
        if ($resultType === BulkApiOptions::SUCCESSFUL_RESULTS) {
            $this->type = BulkApiOptions::SUCCESSFUL_RESULTS;
        } else {
            $this->type = BulkApiOptions::UNSUCCESSFUL_RESULTS;
        }
    }

    public function defineEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}/".$this->type === BulkApiOptions::SUCCESSFUL_RESULTS ? BulkApiOptions::SUCCESSFUL_RESULTS : BulkApiOptions::UNSUCCESSFUL_RESULTS;
    }
}
