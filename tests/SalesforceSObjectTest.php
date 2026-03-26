<?php

beforeEach(function () {
    getAPI();
});

test('Can describe all SObjects available on an instance', function () {
    $api = getAPI()->getSObjectApi();
    $objects = $api->listObjects();

    expect($objects)->toHaveKey('sobjects');
});

test('Can get basic SObject information', function () {
    $api = getAPI()->getSObjectApi();
    $objectInformation = $api->getObjectInformation('Account');

    expect($objectInformation)->toHaveKey('objectDescribe.label', 'Account');
    expect($objectInformation)->toHaveKey('objectDescribe.name', 'Account');
});

test('Can describe a custom SObject', function () {
    $api = getAPI()->getSObjectApi();
    $objectInformation = $api->describeObject('Account');

    expect($objectInformation)->tohaveKeys(['label', 'fields', 'recordTypeInfos', 'childRelationships']);
});

test('Can get a more concise list of fields from an SObject', function () {
    $api = getAPI()->getSObjectApi();
    $fieldInformation = $api->getObjectFields('Account');
    // expect 6 default fields to be appearing in here
    expect($fieldInformation[0])->toHaveCount(6);
});

test('Can request a more specific set of field keys from an SObject (add scale to selection list)', function () {
    $api = getAPI()->getSObjectApi();
    $fieldInformation = $api->getObjectFields('Account', ['scale']);
    // expect 7 fields (6 default + scale)
    expect($fieldInformation[0])->toHaveCount(7);
});

test('Can create a record', function () {
    $api = getAPI();
    $faker = Faker\Factory::create();

    $response = $api->createRecord('Account', [
        'Name' => $faker->company(),
        'Website' => $faker->url(),
    ]);

    expect($response)->toHaveKey('success', true);
});

test('Can create multiple records', function () {
    $api = getAPI();
    $faker = Faker\Factory::create();

    $response = $api->createRecords('Account', [
        ['Name' => $faker->company(), 'Website' => $faker->url()],
        ['Name' => $faker->company(), 'Website' => $faker->url()],
    ]);
    expect($response)->toHaveCount(2);
});

test('Throws on non-existent records', function () {
    $api = getAPI();
    $api->getRecord('Account', 'TestNotExisting', ['Id']);
})->throws(\Saloon\Exceptions\Request\Statuses\NotFoundException::class);
