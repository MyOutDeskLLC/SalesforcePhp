<?php

namespace myoutdeskllc\SalesforcePhp\Requests\SObjects;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use myoutdeskllc\SalesforcePhp\Constants\SoqlDates;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetDeletedRecords extends SaloonRequest
{
    protected ?string $object;
    protected ?string $method = Saloon::GET;
    protected ?string $connector = SalesforceConnector::class;

    public function __construct(string $object)
    {
        $this->object = $object;
    }

    public function defineEndpoint(): string
    {
        return "sobjects/{$this->object}/deleted";
    }

    public function defaultQuery(): array
    {
        return [
            'start' => SoqlDates::THIS_MONTH,
            'end'   => SoqlDates::THIS_MONTH,
        ];
    }
}
