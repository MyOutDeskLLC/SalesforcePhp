<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class DeleteFolder extends SaloonRequest
{
    protected ?string $folderId = null;
    protected ?string $method = Saloon::DELETE;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $folderId)
    {
        $this->folderId = $folderId;
    }

    public function defineEndpoint(): string
    {
        return "/folders/{$this->folderId}";
    }
}
