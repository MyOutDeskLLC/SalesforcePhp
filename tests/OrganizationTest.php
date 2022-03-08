<?php

test('Can query organizational limits', function () {
    $api = getAPI();
    // Assert just a few keys should exist, well know the endpoint works
    expect($api->getLimits())->toHaveKeys(['ConcurrentAsyncGetReportInstances', 'HourlyAsyncReportRuns', 'HourlyDashboardStatuses']);
});

test('Can query supported APIs', function () {
    $api = getAPI();
    // This will contain a lot of information on APIs available, so we'll just make sure it's not empty
    expect($api->listApiVersionsAvailable())->not()->toBeEmpty();
});
