<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;


use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexLogs extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/tooling/query/';
    }

    public function defaultQuery(): array
    {
        return [
            'q' => 'SELECT Application, DurationMilliseconds, Id, LastModifiedDate, Location, LogLength, LogUserId, Operation, Request, StartTime, Status, SystemModstamp FROM ApexLog',
        ];
    }
}
