<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetMetadataComponentDependency extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return '/query';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => 'SELECT MetadataComponentId,MetadataComponentName,MetadataComponentNamespace,MetadataComponentType,RefMetadataComponentId,RefMetadataComponentName,RefMetadataComponentNamespace,RefMetadataComponentType FROM MetadataComponentDependency',
        ];
    }
}
