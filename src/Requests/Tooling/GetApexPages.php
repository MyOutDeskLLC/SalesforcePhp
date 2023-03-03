<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexPages extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/tooling/query';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => 'SELECT ApiVersion, ControllerKey, ControllerType, CreatedById, CreatedDate, Description, Id, IsAvailableInTouch, IsConfirmationTokenRequired, LastModifiedById, LastModifiedDate, Markup, MasterLabel, Name, NamespacePrefix, SystemModstamp FROM ApexPage',
        ];
    }
}
