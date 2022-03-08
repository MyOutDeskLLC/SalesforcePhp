<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

use myoutdeskllc\SalesforcePhp\Requests\Auth\LoginApiUser;

class SalesforceApiUserLogin
{
    protected string $baseUrl = 'https://test.salesforce.com/services/oauth2/token';
    protected string $consumerKey;
    protected string $consumerSecret;

    public function construct(bool $productionMode = false)
    {
        if($productionMode) {
            $this->baseUrl = 'https://login.salesforce.com/services/oauth2/token';
        }
    }

    /**
     * Configure this application based on data in the connected app area
     *
     * @param string $consumerKey
     * @param string $consumerSecret
     * @return void
     */
    public function configureApp(string $consumerKey, string $consumerSecret) : void
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Authenticate the api user with the given username, password
     *
     * Returns an array containing the result (bool), instance URL, token. Pass this instance URL
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public function authenticateUser(string $username, string $password) : array
    {
        $request = new LoginApiUser();
        $request->setData([
            'grant_type'    => "password",
            'client_id'     => $this->consumerKey,
            'client_secret' => $this->consumerSecret,
            'username'      => $username,
            'password'      => $password,
        ]);

        try {
            $response = $request->send()->json();
        } catch (\Exception $exception) {
            return [
                'result' => false,
                'access_token' => null,
                'instance_url' => null,
                'message' => $exception->getMessage()
            ];
        }

        return [
            'result' => true,
            'access_token' => $response['access_token'] ?? null,
            'instance_url' => $response['instance_url'] ?? null,
            'message' => 'success'
        ];
    }
}