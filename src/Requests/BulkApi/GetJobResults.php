<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use myoutdeskllc\SalesforcePhp\Constants\BulkApiConstants;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class GetJobResults extends \Sammyjo20\Saloon\Http\SaloonRequest
{
    protected ?string $id = null;
    protected ?string $type = null;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $id, string $resultType)
    {
        $this->id = $id;
        if($resultType === BulkApiConstants::SUCCESSFUL_RESULTS) {
            $this->type = BulkApiConstants::SUCCESSFUL_RESULTS;
        } else {
            $this->type = BulkApiConstants::UNSUCCESSFUL_RESULTS;
        }
    }

    public function defineEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}/" . $this->type === BulkApiConstants::SUCCESSFUL_RESULTS ? BulkApiConstants::SUCCESSFUL_RESULTS : BulkApiConstants::UNSUCCESSFUL_RESULTS;
    }
}