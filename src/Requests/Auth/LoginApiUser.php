<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Auth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class LoginApiUser extends Request implements HasBody
{
    use HasFormBody;
    protected Method $method = Method::POST;

    public function defaultBody(): array
    {
        return [
            'grant_type'    => 'password',
            'client_id'     => '',
            'client_secret' => '',
            'username'      => '',
            'password'      => '',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/services/oauth2/token';
    }
}
