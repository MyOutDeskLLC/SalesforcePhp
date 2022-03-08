<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Query;

class ExplainQuery extends QueryRequest
{
    public function defaultQuery(): array
    {
        return [
            'explain' => $this->query
        ];
    }
}