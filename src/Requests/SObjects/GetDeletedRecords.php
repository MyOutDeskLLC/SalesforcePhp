<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use myoutdeskllc\SalesforcePhp\Constants\SoqlDates;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDeletedRecords extends Request
{
    protected ?string $object;
    protected Method $method = Method::GET;


    public function __construct(string $object)
    {
        $this->object = $object;
    }

    public function resolveEndpoint(): string
    {
        return "sobjects/{$this->object}/deleted";
    }

    public function defaultQuery(): array
    {
        return [
            'start' => SoqlDates::THIS_MONTH,
            'end' => SoqlDates::THIS_MONTH,
        ];
    }
}
