<?php

beforeEach(function () {
    getAPI();
});

afterAll(function () {
    destroyPestPhpSalesforceChanges();
});

test('it can query folders', function () {
    $reportApi = getAPI()->getReportApi();
    $folders = $reportApi->listFolders();

    expect($folders)->not()->toBeEmpty();
});

test('it can create report folder(s)', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createReportFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getFolderByName($temporaryName);

    expect($folder)->not()->toBeNull();
    expect($folder)->toHaveKey('Name', $temporaryName);
});

test('it can create dashboard folder(s)', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createDashboardFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getDashboardFolderByName($temporaryName);

    expect($folder)->not()->toBeNull();
});

test('it can delete folder(s)', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createReportFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getFolderByName($temporaryName);

    expect($folder)->not()->toBeNull();

    expect($reportApi->deleteFolder($folder['Id']))->toBe(true);
});

test('it can find folders by name', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createReportFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getFolderByName($temporaryName);

    expect($folder)->toHaveKey('Name', $temporaryName);
});

test('it returns null when folders are not found', function () {
    $reportApi = getAPI()->getReportApi();
    $foldersAvailable = $reportApi->recordsOnly()->getFolderByName('This is not a real folder');

    expect($foldersAvailable)->toBeNull();
});

test('it can find reports', function () {
    $reportApi = getAPI()->getReportApi();
    $reports = $reportApi->recordsOnly()->listReports();

    // Just verify the API returns an array (scratch org may not have reports)
    expect($reports)->toBeArray();
});

test('it returns null when a report cannot be found', function () {
    $reportApi = getAPI()->getReportApi();
    $report = $reportApi->recordsOnly()->getReportByName('Games Done Quick 2020');

    expect($report)->toBeNull();
});

test('it can create a folder for dashboards', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createDashboardFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getDashboardFolderByName($temporaryName);

    expect($folder)->not()->toBe(null);
});

test('it can get a list of dashboards', function () {
    $reportApi = getAPI()->getReportApi();
    $dashboards = $reportApi->recordsOnly()->listDashboards();

    // Just verify the API returns an array (scratch org may not have dashboards)
    expect($dashboards)->toBeArray();
});
