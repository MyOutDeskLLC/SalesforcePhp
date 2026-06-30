<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Fetches the next batch of records from a paginated query (query or queryAll)
 * using the nextRecordsUrl returned by salesforce.
 *
 * Salesforce returns nextRecordsUrl as an absolute path, e.g.
 *   /services/data/v51.0/query/01gD0000002HU6KIAW-2000
 *
 * The connector base url already includes /services/data/{version}, and saloon
 * does not allow absolute urls, so that prefix is stripped here.
 */
class QueryMore extends Request
{
    protected Method $method = Method::GET;
    protected string $nextRecordsUrl;

    public function __construct(string $nextRecordsUrl)
    {
        $this->nextRecordsUrl = $nextRecordsUrl;
    }

    public function resolveEndpoint(): string
    {
        // strip the /services/data/{version} prefix so saloon resolves against the base url
        $endpoint = preg_replace('#^.*/services/data/v[0-9.]+#', '', $this->nextRecordsUrl);

        return '/'.ltrim($endpoint, '/');
    }
}
