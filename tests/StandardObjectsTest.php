<?php

beforeEach(function () {
    getAPI();
});

test('it can create records', function() {
    $api = getAPI();
    $response = $api->createRecord('Account', [
        'Name' => 'Test Account' . time(),
        'Site' => 'https://www.example.com'
    ]);
    expect($response['id'])->not()->toBeNull();
});

test('it can create multiple records', function() {
    $seed = time();
    $api = getAPI();
    $response = $api->createRecords('Account', [
        ['Name' => 'Test Account ' . $seed + 1, 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account ' . $seed + 2, 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account ' . $seed + 3, 'Site' => 'https://www.example.com'],
    ]);
    expect($response)->toHaveCount(3);
});

test('it can delete a record', function() {
    $api = getAPI();
    $response = $api->createRecord('Account', [
        'Name' => 'Test Account' . time(),
        'Site' => 'https://www.example.com'
    ]);
    $deleteResponse = $api->deleteRecord('Account', $response['id']);
    expect($deleteResponse)->toBeTrue();
});

test('it can delete multiple records', function() {
    $seed = time();
    $api = getAPI();
    $response = $api->createRecords('Account', [
        ['Name' => 'Test Account ' . $seed + 1, 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account ' . $seed + 2, 'Site' => 'https://www.example.com'],
        ['Name' => 'Test Account ' . $seed + 3, 'Site' => 'https://www.example.com'],
    ]);
    $deleteResponse = $api->deleteRecords('Account', array_column($response, 'id'));

    expect($deleteResponse)->toHaveCount(3);
    foreach($deleteResponse as $itemDeleted) {
        expect($itemDeleted['success'])->toBeTrue();
    }
});
