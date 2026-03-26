<?php

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;

beforeEach(function () {
    getAPI();
});

test('Can create a bulk API job', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setObject('Account');
    $salesforceJob->setOperation(BulkApiOptions::INSERT);
    $salesforceJob->initJob();

    expect($salesforceJob->getJobId())->not()->toBeNull();
});

test('has proper csv data in upload stream', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setCsvFile(__DIR__.'/fixtures/accounts.csv');
    expect($salesforceJob->getUploadStream()->toString())->toContain('Test Account Bulk 1');
});

test('has proper csv data in raw file read stream', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setFileStream(fopen(__DIR__.'/fixtures/accounts.csv', 'r'));
    expect($salesforceJob->getUploadStream()->toString())->toContain('Test Account Bulk 1');
});

test('has proper csv data when set from an array', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setRecordsToUpload([
        ['Test Account Array', 'https://example.com'],
    ]);
    expect($salesforceJob->getUploadStream()->toString())->toContain('Test Account Array');
});

test('has proper csv data when records are pushed one at a time', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setRecordsToUpload([
        ['Test Account One', 'https://example1.com'],
        ['Test Account Two', 'https://example2.com'],
    ]);
    expect($salesforceJob->getUploadStream()->toString())->toContain('Test Account One', 'Test Account Two');
});
