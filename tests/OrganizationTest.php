<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('Can query organizational limits', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('organization/limits'),
    ]);
    $api = getAPI($mockClient);
    expect($api->getLimits())->toHaveKeys(['ConcurrentAsyncGetReportInstances', 'HourlyAsyncReportRuns', 'HourlyDashboardStatuses']);
});

test('Can query supported APIs', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('organization/api_versions'),
    ]);
    $api = getAPI($mockClient);
    expect($api->listApiVersionsAvailable())->not()->toBeEmpty();
});
