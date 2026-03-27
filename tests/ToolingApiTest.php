<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('Can query list of apex classes', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('tooling/apex_classes'),
    ]);
    $api = getAPI($mockClient)->getToolingApi();
    $classes = $api->getApexClasses();

    expect($classes)->toBeArray();
});

test('Can execute anonymous apex', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('tooling/execute_anonymous'),
    ]);
    $api = getAPI($mockClient)->getToolingApi();
    $result = $api->executeAnonymousApex("System.debug('test');");

    expect($result)->toHaveKey('compiled', true);
    expect($result)->toHaveKey('success', true);
});

test('can query ApexLogs endpoint', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('tooling/apex_logs'),
    ]);
    $api = getAPI($mockClient)->getToolingApi();
    $logs = $api->getApexLogs();

    expect($logs)->toBeArray();
});

test('it can create emails in the public folder', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('tooling/create_email_template'),
    ]);
    $testEmailTemplate = [
        'FullName' => 'unfiled$public/test1234',
        'Metadata' => [
            'subject'     => 'Testing123',
            'available'   => true,
            'name'        => 'NotSureWhatGoesHere',
            'style'       => 0,
            'type'        => 0,
            'encodingKey' => 'utf-8',
        ],
    ];
    $result = getAPI($mockClient)->getToolingApi()->createEmailTemplate($testEmailTemplate);

    expect($result)->toHaveKey('id');
});
