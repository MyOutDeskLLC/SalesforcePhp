<?php

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;

beforeEach(function () {
    getAPI();
});

test('Can query list of apex classes, finding a class named Totoro', function () {
    $api = SalesforceApi::getToolingApi();
    $classes = $api->getApexClasses();

    // Since this is our scratch org, we should find that the classes contain Totoro
    expect($classes)->not()->toBeEmpty();
    expect($classes[0])->toHaveKey('name');
    expect($classes[0]['name'])->toContain('Totoro');
});


test('Can query list of apex pages, finding a page named TotoroPage', function () {
    $api = SalesforceApi::getToolingApi();
    $pages = $api->getApexPages();

    // Since this is our scratch org, we should find a page with Totoro
    expect($pages)->not()->toBeEmpty();
    // It's uppercase for VFpages
    expect($pages[0])->toHaveKey('Name');
    expect($pages[0]['Name'])->toContain('TotoroPage');
});

test('Can execute anonyous apex', function () {
    $api = SalesforceApi::getToolingApi();
    $result = $api->executeAnonymousApex("System.debug('test');");

    expect($result)->toHaveKey('compiled', true);
    expect($result)->toHaveKey('success', true);
});

test('can list ApexLogs', function () {
    $api = SalesforceApi::getToolingApi();
    $logs = $api->getApexLogs();

    expect($logs)->not()->toBeEmpty();
});

test('can get the body of an ApexLog', function () {
    $api = SalesforceApi::getToolingApi();
    $logs = $api->getApexLogs();
    $firstLogBody = $api->getApexLog($logs[0]['Id']);

    expect($firstLogBody)->not()->toBeEmpty();
});

test('can locate ApexClass by name', function () {
    $api = SalesforceApi::getToolingApi();
    $totoroClass = $api->getApexClassByName('Totoro');

    expect($totoroClass)->not()->toBeEmpty();
    expect($totoroClass[0])->toHaveKey('Name', 'Totoro');
});

test('can execute apex tests with specific methods (1 test method)', function () {
    $api = SalesforceApi::getToolingApi();
    $totoroTest = $api->getApexClassByName('TotoroTest');
    $testResults = $api->runTestsSynchronous([[
        'classId' => $totoroTest[0]['Id'],
        'testMethods' => ['canGarden']
    ]]);

    expect($testResults)->not()->toBeEmpty();
    expect($testResults)->toHaveKey('numTestsRun', 1);
});

test('can execute apex tests with just class ID (4 test methods)', function () {
    $api = SalesforceApi::getToolingApi();
    $totoroTest = $api->getApexClassByName('TotoroTest');
    $testResults = $api->runTestsSynchronous([[
        'classId' => $totoroTest[0]['Id']
    ]]);

    expect($testResults)->not()->toBeEmpty();
    expect($testResults)->toHaveKey('numTestsRun', 4);
});

test('can execute apex tests async by using an array of ids', function () {
    $api = SalesforceApi::getToolingApi();
    $totoroTest = $api->getApexClassByName('TotoroTest');
    $asyncRun = $api->runTestsAsynchronousById([$totoroTest[0]['Id']]);

    // Since this is a string for reasons unknown, and includes quotes. So just drop them.
    expect($asyncRun)->not()->toBeEmpty();
    expect($asyncRun)->toStartWith('707');
});

/**
 * Creating emails is awful and makes no sense. I don't recommend doing it via the API.
 * Your data should contain a FullName that resolves to a folder path
 *
 * For public, this would mean the full name InvoiceTemplate would be:
 * unfiled$public/InvoiceTemplate
 *
 * style and type keys are supposed to be const values, but they are integer values instead
 *
 * Final payload will look something like the below tests
 *
 */
test('it can create emails in the public folder', function() {
    $faker = Faker\Factory::create();

    $testEmailTemplate = [
        'FullName' => 'unfiled$public/test' . $faker->randomNumber(4),
        'Metadata' => [
            // If you add spaces here, it appears to fail
            'subject' => 'Testing123',
            'available' => true,
            'name' => 'NotSureWhatGoesHere',
            'style' => 0, // maps to none
            'type' => 0, // maps to none
            'encodingKey' => 'utf-8'
        ]
    ];
    $result = SalesforceApi::getToolingApi()->createEmailTemplate($testEmailTemplate);

    expect($result)->toHaveKey('id');
});

/**
 * Note on why these are commented out
 *
 * Salesforce testing queue is singular, meaning if we run these the tests will actually already be in queue.
 *
 * The response type is a string! Please keep that in mind. It was unexpected.
 */
//
//test('can execute apex tests async by using an array class names', function () {
//    $api = SalesforceApi::getToolingApi();
//    $asyncRun = $api->runTestsAsynchronousByClassNames(['TotoroTest']);
//
//    // Since this is a string for reasons unknown, just check that it's a salesforce id for a test run
//    expect($asyncRun)->not()->toBeEmpty();
//    expect($asyncRun)->toStartWith('707');
//});