<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

/**
 * TODO: These yield "HTTP Method not allowed" despite docs.
 *
 * It seemed to suggest that it would be at /analytics/folders instead of folders, but neither seemed to work
 *
 * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_folders_reference_resource.htm
 */
class ListFolders extends SaloonRequest
{
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return '/folders/';
    }
}
