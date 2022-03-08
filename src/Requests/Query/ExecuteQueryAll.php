<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

class ExecuteQueryAll extends QueryRequest
{
    public function defineEndpoint(): string
    {
        return '/queryAll';
    }
}
