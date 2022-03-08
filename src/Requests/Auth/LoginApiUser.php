<?php

namespace myoutdeskllc\SalesforcePhp\Requests\Auth;

use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use myoutdeskllc\SalesforcePhp\Connectors\ApiUserConnector;
use Sammyjo20\Saloon\Traits\Plugins\HasFormParams;

class LoginApiUser extends SaloonRequest
{
    use HasFormParams;

    protected ?string $method = Saloon::POST;
    protected ?string $connector = ApiUserConnector::class;

    public function defaultData(): array
    {
        return [
            'grant_type'    => "password",
            'client_id'     => '',
            'client_secret' => '',
            'username'      => '',
            'password'      => '',
        ];
    }

    public function defineEndpoint(): string
    {
        return '/services/oauth2/token';
    }
}