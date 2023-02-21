<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexTestRunResults extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
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
