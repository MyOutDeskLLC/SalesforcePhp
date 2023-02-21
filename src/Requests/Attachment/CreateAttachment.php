<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Attachment;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;


class CreateAttachment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return 'Attachment';
    }

    public function defaultBody(): array
    {
        return [
            'Name' => '',
            'Body' => base64_encode(''),
            'ContentType' => 'application/pdf',
            'Description' => '',
            'ParentId' => '',
        ];
    }
}
