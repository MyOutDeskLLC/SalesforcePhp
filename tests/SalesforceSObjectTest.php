<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('Can describe all SObjects available on an instance', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/list_objects'),
    ]);
    $api = getAPI($mockClient)->getSObjectApi();
    $objects = $api->listObjects();

    expect($objects)->toHaveKey('sobjects');
});

test('Can get basic SObject information', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/object_information'),
    ]);
    $api = getAPI($mockClient)->getSObjectApi();
    $objectInformation = $api->getObjectInformation('Account');

    expect($objectInformation)->toHaveKey('objectDescribe.label', 'Account');
    expect($objectInformation)->toHaveKey('objectDescribe.name', 'Account');
});

test('Can describe a custom SObject', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/describe_object'),
    ]);
    $api = getAPI($mockClient)->getSObjectApi();
    $objectInformation = $api->describeObject('Account');

    expect($objectInformation)->tohaveKeys(['label', 'fields', 'recordTypeInfos', 'childRelationships']);
});

test('Can get a more concise list of fields from an SObject', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/describe_object_fields'),
    ]);
    $api = getAPI($mockClient)->getSObjectApi();
    $fieldInformation = $api->getObjectFields('Account');
    // expect 6 default fields to be appearing in here
    expect($fieldInformation[0])->toHaveCount(6);
});

test('Can request a more specific set of field keys from an SObject (add scale to selection list)', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/describe_object_fields_scale'),
    ]);
    $api = getAPI($mockClient)->getSObjectApi();
    $fieldInformation = $api->getObjectFields('Account', ['scale']);
    // expect 7 fields (6 default + scale)
    expect($fieldInformation[0])->toHaveCount(7);
});

test('Can create a record', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/create_record'),
    ]);
    $api = getAPI($mockClient);

    $response = $api->createRecord('Account', [
        'Name' => 'Test Company',
        'Website' => 'https://example.com',
    ]);

    expect($response)->toHaveKey('success', true);
});

test('Can create multiple records', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/create_records'),
    ]);
    $api = getAPI($mockClient);

    $response = $api->createRecords('Account', [
        ['Name' => 'Test Company 1', 'Website' => 'https://example1.com'],
        ['Name' => 'Test Company 2', 'Website' => 'https://example2.com'],
    ]);
    expect($response)->toHaveCount(2);
});

test('Throws on non-existent records', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('sobject/not_found'),
    ]);
    $api = getAPI($mockClient);
    $api->getRecord('Account', 'TestNotExisting', ['Id']);
})->throws(\Saloon\Exceptions\Request\Statuses\NotFoundException::class);
