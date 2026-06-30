<?php

use myoutdeskllc\SalesforcePhp\Requests\Query\QueryMore;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('QueryMore strips the services/data/version prefix from nextRecordsUrl', function () {
    $request = new QueryMore('/services/data/v51.0/query/01gD0000002HU6KIAW-2000');

    expect($request->resolveEndpoint())->toBe('/query/01gD0000002HU6KIAW-2000');
});

test('QueryMore handles the nextRecordsUrl returned by queryAll', function () {
    // per salesforce docs, queryAll returns a /query/ nextRecordsUrl, not /queryAll/
    // https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_queryall.htm
    $request = new QueryMore('/services/data/v67.0/query/01gRO0000016PIAYA2-500');

    expect($request->resolveEndpoint())->toBe('/query/01gRO0000016PIAYA2-500');
});

test('QueryMore handles a fully qualified nextRecordsUrl', function () {
    $request = new QueryMore('https://test.salesforce.com/services/data/v51.0/query/01gD0000002HU6KIAW-2000');

    expect($request->resolveEndpoint())->toBe('/query/01gD0000002HU6KIAW-2000');
});

test('getAllRecordsRaw follows pagination until done', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'totalSize'      => 3,
            'done'           => false,
            'nextRecordsUrl' => '/services/data/v51.0/query/01gD0000002HU6KIAW-2000',
            'records'        => [
                ['attributes' => [], 'Id' => '001', 'Name' => 'Page 1 A'],
                ['attributes' => [], 'Id' => '002', 'Name' => 'Page 1 B'],
            ],
        ], 200),
        MockResponse::make([
            'totalSize' => 3,
            'done'      => true,
            'records'   => [
                ['attributes' => [], 'Id' => '003', 'Name' => 'Page 2 A'],
            ],
        ], 200),
    ]);
    $api = getAPI($mockClient);
    $results = $api->getAllRecordsRaw('SELECT Id, Name FROM Account');

    $mockClient->assertSentCount(2);
    expect($results)->toHaveCount(3);
    expect($results[0])->not()->toHaveKey('attributes');
    expect($results[2])->toHaveKey('Name', 'Page 2 A');
});

test('getAllRecordsRaw returns a single page when already done', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'totalSize' => 1,
            'done'      => true,
            'records'   => [
                ['attributes' => [], 'Id' => '001', 'Name' => 'Only Page'],
            ],
        ], 200),
    ]);
    $api = getAPI($mockClient);
    $results = $api->getAllRecordsRaw('SELECT Id, Name FROM Account');

    $mockClient->assertSentCount(1);
    expect($results)->toHaveCount(1);
});

test('getAllRecordsRaw with includeDeleted uses the queryAll endpoint', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'totalSize' => 1,
            'done'      => true,
            'records'   => [['attributes' => [], 'Id' => '001']],
        ], 200),
    ]);
    $api = getAPI($mockClient);
    $api->getAllRecordsRaw('SELECT Id FROM Account', true);

    $mockClient->assertSent(function ($request) {
        return $request->resolveEndpoint() === '/queryAll';
    });
});

test('queryMore fetches the next batch of records', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'totalSize' => 4000,
            'done'      => true,
            'records'   => [
                ['attributes' => [], 'Id' => '001', 'Name' => 'Second Page'],
            ],
        ], 200),
    ]);
    $api = getAPI($mockClient);
    $results = $api->queryMore('/services/data/v51.0/query/01gD0000002HU6KIAW-2000');

    $mockClient->assertSent(function ($request) {
        return $request->resolveEndpoint() === '/query/01gD0000002HU6KIAW-2000';
    });
    expect($results)->not()->toBeEmpty();
});
