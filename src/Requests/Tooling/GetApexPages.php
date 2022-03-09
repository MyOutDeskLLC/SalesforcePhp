<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexPages extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
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
