<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('it can create records', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('standard/create_record'),
    ]);
    $api = getAPI($mockClient);
    $response = $api->createRecord('Account', [
        'Name' => 'Test Account',
        'Site' => 'https://www.example.com',
    ]);
    expect($response['id'])->not()->toBeNull();
});

test('it can create multiple records', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('standard/create_records'),
    ]);
    $api = getAPI($mockClient);
    $response = $api->createRecords('Account', [
        ['Name' => 'Test Account 1', 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account 2', 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account 3', 'Site' => 'https://www.example.com'],
    ]);
    expect($response)->toHaveCount(3);
});

test('it can delete a record', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('standard/create_for_delete'),
        MockResponse::fixture('standard/delete_record'),
    ]);
    $api = getAPI($mockClient);
    $response = $api->createRecord('Account', [
        'Name' => 'Test Account',
        'Site' => 'https://www.example.com',
    ]);
    $deleteResponse = $api->deleteRecord('Account', $response['id']);
    expect($deleteResponse)->toBeTrue();
});

test('it can delete multiple records', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('standard/create_for_batch_delete'),
        MockResponse::fixture('standard/delete_records'),
    ]);
    $api = getAPI($mockClient);
    $response = $api->createRecords('Account', [
        ['Name' => 'Test Account 1', 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account 2', 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account 3', 'Site' => 'https://www.example.com'],
    ]);
    $deleteResponse = $api->deleteRecords(array_column($response, 'id'));

    expect($deleteResponse)->toHaveCount(3);
    foreach ($deleteResponse as $itemDeleted) {
        expect($itemDeleted['success'])->toBeTrue();
    }
});
