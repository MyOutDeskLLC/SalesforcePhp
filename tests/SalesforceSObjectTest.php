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
    $objectInformation = $api->getObjectInformation('Virtual_Youtuber__c');

    expect($objectInformation)->toHaveKey('objectDescribe.label', 'Virtual Youtuber');
    expect($objectInformation)->toHaveKey('objectDescribe.name', 'Virtual_Youtuber__c');
});

test('Can describe a custom SObject', function () {
    $api = getAPI()->getSObjectApi();
    $objectInformation = $api->describeObject('Virtual_Youtuber__c');

    expect($objectInformation)->tohaveKeys(['label', 'fields', 'recordTypeInfos', 'childRelationships']);
});

test('Can get a more concise list of fields from an SObject', function () {
    $api = getAPI()->getSObjectApi();
    $fieldInformation = $api->getObjectFields('Virtual_Youtuber__c');
    // expect 6 default fields to be appearing in here
    expect($fieldInformation[0])->toHaveCount(6);
});

test('Can request a more specific set of field keys from an SObject (add scale to selection list)', function () {
    $api = getAPI()->getSObjectApi();
    $fieldInformation = $api->getObjectFields('Virtual_Youtuber__c', ['scale']);
    // expect 6 default fields to be appearing in here
    expect($fieldInformation[0])->toHaveCount(7);
});

test('Can create a record', function () {
    $api = getAPI();
    $faker = Faker\Factory::create();

    $response = $api->createRecord('Virtual_Youtuber__c', [
        'name'       => $faker->name,
        'Twitter__c' => $faker->randomAscii(5),
    ]);

    expect($response)->toHaveKey('success', true);
});

test('Can create multiple records', function () {
    $api = getAPI();
    $faker = Faker\Factory::create();

    $response = $api->createRecords('Virtual_Youtuber__c', [
        ['name' => $faker->firstName, 'Twitter__c' => $faker->randomAscii(5)],
        ['name' => $faker->lastName, 'Twitter__c' => $faker->randomAscii(5)],
    ]);
    expect($response)->toHaveCount(2);
});

test('Throws on non-existent records', function () {
    $api = getAPI();
    $api->getRecord('Virtual_Youtuber__c', 'TestNotExisting', ['Id']);
})->throws(\Saloon\Exceptions\Request\Statuses\NotFoundException::class);
