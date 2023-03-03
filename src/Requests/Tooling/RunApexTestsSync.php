<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class RunApexTestsSync extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/tooling/runTestsSynchronous';
    }

    public function defaultBody(): array
    {
        return [
            [
                'classId'     => 'nonExistentClassId',
                'testMethods' => ['TestMethod1'],
            ],
        ];
    }
}
