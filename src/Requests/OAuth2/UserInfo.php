<?php

namespace myoutdeskllc\SalesforcePhp\Requests\OAuth2;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class UserInfo extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/services/oauth2/userinfo';
    }
}
