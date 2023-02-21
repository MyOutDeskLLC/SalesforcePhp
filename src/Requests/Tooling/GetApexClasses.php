<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Tooling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetApexClasses extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/tooling/apexManifest';
    }
}
