<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteFolder extends Request
{
    protected ?string $folderId = null;
    protected Method $method = Method::DELETE;

    public function __construct(string $folderId)
    {
        $this->folderId = $folderId;
    }

    public function resolveEndpoint(): string
    {
        return "/folders/{$this->folderId}";
    }
}
