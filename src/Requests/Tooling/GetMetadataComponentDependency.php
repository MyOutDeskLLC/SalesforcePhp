<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;


use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetMetadataComponentDependency extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
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
