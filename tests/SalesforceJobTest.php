<?php

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;

beforeEach(function () {
    getAPI();
});

test('Can create a bulk API job', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setObject('Virtual_Youtuber__c');
    $salesforceJob->setOperation(BulkApiOptions::INSERT);
    $salesforceJob->initJob();

    expect($salesforceJob->getJobId())->not()->toBeNull();
});

test('has proper csv data in upload stream', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setCsvFile(__DIR__.'/fixtures/vtubers.csv');
    expect($salesforceJob->getUploadStream()->toString())->toContain('sakura miko');
});

test('has proper csv data in raw file read stream', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setFileStream(fopen(__DIR__.'/fixtures/vtubers.csv', 'r'));
    expect($salesforceJob->getUploadStream()->toString())->toContain('sakura miko');
});

test('has proper csv data when set from an array', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setRecordsToUpload([
        ['Sakura Miko', '@sakuramiko35'],
    ]);
    expect($salesforceJob->getUploadStream()->toString())->toContain('Sakura Miko');
});

test('has proper csv data when records are pushed one at a time', function () {
    $api = getAPI()->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setRecordsToUpload([
        ['Sakura Miko', '@sakuramiko35'],
        ['usadapekora', '@test'],
    ]);
    expect($salesforceJob->getUploadStream()->toString())->toContain('Sakura Miko', 'usadapekora');
});
