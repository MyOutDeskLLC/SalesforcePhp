<?php

beforeEach(function () {
    getAPI();
});

beforeAll(function () {
    $api = getAPI();
    // Create a known account for search tests
    $api->createRecord('Account', [
        'Name' => 'SalesforcePhp Test',
        'Website' => 'https://salesforcephp.test',
    ]);
    // Search index needs a moment to update
    sleep(3);
});

test('Can search all records for SalesforcePhp', function () {
    $api = getAPI();
    $results = $api->search('SalesforcePhp');

    expect($results['searchRecords'])->not()->toBeEmpty();
});

test('Can search just accounts for SalesforcePhp', function () {
    $api = getAPI();
    $results = $api->searchIn('SalesforcePhp', 'Account');

    expect($results['searchRecords'])->not()->toBeEmpty();
});

test('Can search just accounts and supply additional fields to select', function () {
    $api = getAPI();
    $results = $api->searchIn('SalesforcePhp', 'Account', ['Name']);

    expect($results['searchRecords'])->not()->toBeEmpty();
    expect($results['searchRecords'][0])->toHaveKey('Name');
});

test('Can search using helper function searchObjectWhereProperties', function () {
    $api = getAPI();
    $results = $api->searchObjectWhereProperties('Account', ['name' => 'SalesforcePhp Test'], ['Id', 'Name']);

    expect($results)->not()->toBeEmpty();
    expect($results[0])->toHaveKey('Name', 'SalesforcePhp Test');
});

test('Can find a single record and return properties directly', function () {
    $api = getAPI();
    $result = $api->findRecord('Account', ['name' => 'SalesforcePhp Test'], ['Id', 'Name']);

    expect($result)->toHaveKey('Name', 'SalesforcePhp Test');
});

test('Returns null if it fails to find a record', function () {
    $api = getAPI();
    $result = $api->findRecord('Account', ['name' => 'Not A Real Record'], ['Id', 'Name']);

    expect($result)->toBeNull();
});
