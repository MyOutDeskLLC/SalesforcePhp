<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use myoutdeskllc\SalesforcePhp\Constants\BulkApiConstants;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class UpdateJobState extends \Sammyjo20\Saloon\Http\SaloonRequest
{
    use HasJsonBody;

    protected ?string $id = null;
    protected ?string $state = BulkApiConstants::UPLOAD_COMPLETE;
    protected ?string $method = Saloon::PATCH;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $id, string $newJobState)
    {
        $this->id = $id;
        $this->state = $newJobState;
    }

    public function defaultData(): array
    {
        return [
            'state' => $this->state
        ];
    }

    public function defineEndpoint(): string
    {
        return "/jobs/ingest/{$this->id}";
    }
}