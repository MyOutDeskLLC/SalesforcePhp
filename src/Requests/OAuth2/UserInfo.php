<?php

namespace myoutdeskllc\SalesforcePhp\Requests\OAuth2;

use myoutdeskllc\SalesforcePhp\SalesforceApi;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class UserInfo extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return SalesforceApi::getInstanceUrl().'/services/oauth2/userinfo';
    }
}