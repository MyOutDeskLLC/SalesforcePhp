<?php

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('Can create a bulk API job', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('bulk/create_job'),
    ]);
    $api = getAPI($mockClient)->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setObject('Account');
    $salesforceJob->setOperation(BulkApiOptions::INSERT);

    $salesforceJob->initJob();

    expect($salesforceJob->getJobId())->not()->toBeNull();
});

test('Can upload records to a bulk API job', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('bulk/create_job_upload'),
        MockResponse::fixture('bulk/upload_data'),
        MockResponse::fixture('bulk/close_job'),
    ]);
    $api = getAPI($mockClient)->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setObject('Account');
    $salesforceJob->setOperation(BulkApiOptions::INSERT);
    $salesforceJob->initJob();

    $salesforceJob->setCsvFile(__DIR__.'/fixtures/accounts.csv')->upload();
    $salesforceJob->closeJob();

    expect($salesforceJob->getState())->toEqual('UploadComplete');
});

test('Can get existing job status', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('bulk/create_job_status'),
        MockResponse::fixture('bulk/get_job'),
    ]);
    $api = getAPI($mockClient)->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setObject('Account');
    $salesforceJob->setOperation(BulkApiOptions::INSERT);
    $salesforceJob->initJob();
    // after this, store the ID and throw out the job
    $queriedJob = SalesforceJob::getExistingJobById($salesforceJob->getJobId(), $api);
    // It should contain our object as the target of the job
    expect($queriedJob->getObject())->toEqual('Account');
});

test('Can abort a job', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('bulk/create_job_abort'),
        MockResponse::fixture('bulk/get_job_abort'),
        MockResponse::fixture('bulk/abort_job'),
    ]);
    $api = getAPI($mockClient)->getBulkApi();
    $salesforceJob = new SalesforceJob($api);
    $salesforceJob->setObject('Account');
    $salesforceJob->setOperation(BulkApiOptions::INSERT);
    $salesforceJob->initJob();
    // after this, store the ID and throw out the job
    $queriedJob = SalesforceJob::getExistingJobById($salesforceJob->getJobId(), $api);
    // It should contain our object as the target of the job
    $queriedJob->abortJob();
    expect($queriedJob->getState())->toEqual('Aborted');
});
