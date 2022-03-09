<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetApexTestRunResults extends SaloonRequest
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
            'q' => 'SELECT AsyncApexJobId, ClassesCompleted, ClassesEnqueued, CreatedById, CreatedDate, EndTime, Id, IsAllTests, IsDeleted, JobName, LastModifiedById, LastModifiedDate, MethodsCompleted, MethodsEnqueued, MethodsFailed, Source, StartTime, Status, SystemModstamp, TestTime, UserId FROM ApexTestRunResult',
        ];
    }
}
