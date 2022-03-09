<?php

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;

beforeEach(function () {
    getAPI();
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