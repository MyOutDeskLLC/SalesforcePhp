<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexLogs extends SaloonRequest
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
            'q' => 'SELECT Application, DurationMilliseconds, Id, LastModifiedDate, Location, LogLength, LogUserId, Operation, Request, StartTime, Status, SystemModstamp FROM ApexLog'
        ];
    }
}