<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;


use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetEmailTemplates extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/tooling/query/';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => 'SELECT ApiVersion, CreatedById, CreatedDate, Description, Id, LastModifiedById, LastModifiedDate, Name, NamespacePrefix, RelatedEntityType, Subject FROM EmailTemplate',
        ];
    }
}
