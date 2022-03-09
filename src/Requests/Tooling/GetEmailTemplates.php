<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetEmailTemplates extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;


    public function defineEndpoint(): string
    {
        return "/tooling/query/";
    }

    public function defaultQuery(): array
    {
        return [
            'q' => 'SELECT ApiVersion, CreatedById, CreatedDate, Description, Id, LastModifiedById, LastModifiedDate, Name, NamespacePrefix, RelatedEntityType, Subject FROM EmailTemplate'
        ];
    }
}