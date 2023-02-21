<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Analytics;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * TODO: These yield "HTTP Method not allowed" despite docs.
 *
 * It seemed to suggest that it would be at /analytics/folders instead of folders, but neither seemed to work
 *
 * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_folders_reference_resource.htm
 */
class ListFolders extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/folders/';
    }
}
