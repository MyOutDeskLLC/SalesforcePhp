<?php

beforeEach(function () {
    getAPI();
});

test('Can query list of apex classes', function () {
    $api = getAPI()->getToolingApi();
    $classes = $api->getApexClasses();

    expect($classes)->toBeArray();
});

test('Can execute anonymous apex', function () {
    $api = getAPI()->getToolingApi();
    $result = $api->executeAnonymousApex("System.debug('test');");

    expect($result)->toHaveKey('compiled', true);
    expect($result)->toHaveKey('success', true);
});

test('can query ApexLogs endpoint', function () {
    $api = getAPI()->getToolingApi();
    $logs = $api->getApexLogs();

    // ApexLogs requires a trace flag to capture logs, so we just verify the API call works
    expect($logs)->toBeArray();
});

test('it can create emails in the public folder', function () {
    $faker = Faker\Factory::create();

    $testEmailTemplate = [
        'FullName' => 'unfiled$public/test'.$faker->randomNumber(4),
        'Metadata' => [
            'subject'     => 'Testing123',
            'available'   => true,
            'name'        => 'NotSureWhatGoesHere',
            'style'       => 0,
            'type'        => 0,
            'encodingKey' => 'utf-8',
        ],
    ];
    $result = getAPI()->getToolingApi()->createEmailTemplate($testEmailTemplate);

    expect($result)->toHaveKey('id');
});
