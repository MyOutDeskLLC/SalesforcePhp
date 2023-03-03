<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

class ExecuteQueryAll extends QueryRequest
{
    public function resolveEndpoint(): string
    {
        return '/queryAll';
    }
}
