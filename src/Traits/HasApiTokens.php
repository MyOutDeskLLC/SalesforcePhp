<?php

namespace myoutdeskllc\SalesforcePhp\Traits;

trait HasApiTokens
{
    protected static string $token;
    protected static string $instanceUrl;
    protected static ?string $apiVersion;

    /**
     * Sets the API version in use. Call this after listApiVersionsAvailable() if you are not sure what your org supports.
     *
     * @param string $apiVersion API version, with or without v
     *
     * @return void
     */
    public function setApiVersion(string $apiVersion): void
    {
        self::$apiVersion = 'v'.str_replace($apiVersion, 'v', '');
    }

    public static function token(): string
    {
        return self::$token;
    }

    public static function instanceUrl()
    {
        return self::$instanceUrl.'/services/data/'.self::$apiVersion;
    }
}
