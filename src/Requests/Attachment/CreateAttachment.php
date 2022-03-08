<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Attachment;

use Carbon\Carbon;
use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;

class CreateAttachment extends SaloonRequest
{
    use HasJsonBody;

    protected ?string $method = Saloon::POST;
    protected ?string $connector = SalesforceConnector::class;

    public function defineEndpoint(): string
    {
        return 'Attachment';
    }

    public function defaultData(): array
    {
        return [
            'Name'        => '',
            'Body'        => base64_encode(''),
            'ContentType' => 'application/pdf',
            'Description' => '',
            'ParentId'    => '',
        ];
    }
}