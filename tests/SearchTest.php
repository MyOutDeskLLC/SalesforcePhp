<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('Can search all records for SalesforcePhp', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('search/create_test_account'),
        MockResponse::fixture('search/search_all'),
    ]);
    $api = getAPI($mockClient);
    $api->createRecord('Account', [
        'Name' => 'SalesforcePhp Test',
        'Website' => 'https://salesforcephp.test',
    ]);
    sleep(3);
    $results = $api->search('SalesforcePhp');

    expect($results['searchRecords'])->not()->toBeEmpty();
});

test('Can search just accounts for SalesforcePhp', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('search/search_in_account'),
    ]);
    $api = getAPI($mockClient);
    $results = $api->searchIn('SalesforcePhp', 'Account');

    expect($results['searchRecords'])->not()->toBeEmpty();
});

test('Can search just accounts and supply additional fields to select', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('search/search_in_account_fields'),
    ]);
    $api = getAPI($mockClient);
    $results = $api->searchIn('SalesforcePhp', 'Account', ['Name']);

    expect($results['searchRecords'])->not()->toBeEmpty();
    expect($results['searchRecords'][0])->toHaveKey('Name');
});

test('Can search using helper function searchObjectWhereProperties', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('search/query_where_properties'),
    ]);
    $api = getAPI($mockClient);
    $results = $api->searchObjectWhereProperties('Account', ['name' => 'SalesforcePhp Test'], ['Id', 'Name']);

    expect($results)->not()->toBeEmpty();
    expect($results[0])->toHaveKey('Name', 'SalesforcePhp Test');
});

test('Can find a single record and return properties directly', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('search/find_record'),
    ]);
    $api = getAPI($mockClient);
    $result = $api->findRecord('Account', ['name' => 'SalesforcePhp Test'], ['Id', 'Name']);

    expect($result)->toHaveKey('Name', 'SalesforcePhp Test');
});

test('Returns null if it fails to find a record', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('search/find_record_empty'),
    ]);
    $api = getAPI($mockClient);
    $result = $api->findRecord('Account', ['name' => 'Not A Real Record'], ['Id', 'Name']);

    expect($result)->toBeNull();
});
