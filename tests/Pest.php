<?php

use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Connectors\SalesforceApiConnector;
use Saloon\MockConfig;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

MockConfig::setFixturePath('tests/fixtures/responses');

function getAPI(?MockClient $mockClient = null): SalesforceApi
{
    if (file_exists(__DIR__.'/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
    }

    $api = new SalesforceApi(
        $_ENV['SALESFORCE_INSTANCE_URL'] ?? 'https://test.salesforce.com',
        $_ENV['API_VERSION'] ?? 'v51.0'
    );

    $authenticatorFile = __DIR__.'/../.authenticator';

    if (file_exists($authenticatorFile)) {
        $api->restoreExistingOAuthConnection(file_get_contents($authenticatorFile), function ($authenticator) use ($authenticatorFile) {
            file_put_contents($authenticatorFile, SalesforceApi::serializeAuthenticator($authenticator));
        });
    } else {
        // No live credentials - fixtures must already exist
        $connector = new SalesforceApiConnector();
        $connector->withTokenAuth('mock-access-token');
        $api->setConnector($connector);
    }

    if ($mockClient !== null) {
        $api->getConnector()->withMockClient($mockClient);
    }

    $api->recordsOnly();

    return $api;
}

function toFlatArray(array $results, string $key)
{
    return array_map(function ($result) use ($key) {
        return $result[$key];
    }, $results);
}
