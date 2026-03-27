<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('it can query folders', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/list_folders'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $folders = $reportApi->listFolders();

    expect($folders)->not()->toBeEmpty();
});

test('it can create report folder(s)', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/create_report_folder'),
        MockResponse::fixture('report/get_report_folder'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $reportApi->createReportFolder('PESTPHPTestFolder');
    $folder = $reportApi->getFolderByName('PESTPHPTestFolder');

    expect($folder)->not()->toBeNull();
    expect($folder)->toHaveKey('Name', 'PESTPHPTestFolder');
});

test('it can create dashboard folder(s)', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/create_dashboard_folder'),
        MockResponse::fixture('report/get_dashboard_folder'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $reportApi->createDashboardFolder('PESTPHPDashFolder');
    $folder = $reportApi->getDashboardFolderByName('PESTPHPDashFolder');

    expect($folder)->not()->toBeNull();
});

test('it can delete folder(s)', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/create_folder_delete'),
        MockResponse::fixture('report/get_folder_delete'),
        MockResponse::fixture('report/delete_folder'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $reportApi->createReportFolder('PESTPHPDeleteFolder');
    $folder = $reportApi->getFolderByName('PESTPHPDeleteFolder');

    expect($folder)->not()->toBeNull();

    expect($reportApi->deleteFolder($folder['Id']))->toBe(true);
});

test('it can find folders by name', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/create_folder_find'),
        MockResponse::fixture('report/get_folder_find'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $reportApi->createReportFolder('PESTPHPFindFolder');
    $folder = $reportApi->getFolderByName('PESTPHPFindFolder');

    expect($folder)->toHaveKey('Name', 'PESTPHPFindFolder');
});

test('it returns null when folders are not found', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/folder_not_found'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $foldersAvailable = $reportApi->getFolderByName('This is not a real folder');

    expect($foldersAvailable)->toBeNull();
});

test('it can find reports', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/list_reports'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $reports = $reportApi->listReports();

    // Just verify the API returns an array (scratch org may not have reports)
    expect($reports)->toBeArray();
});

test('it returns null when a report cannot be found', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/report_not_found'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $report = $reportApi->getReportByName('Games Done Quick 2020');

    expect($report)->toBeNull();
});

test('it can create a folder for dashboards', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/create_dash_folder2'),
        MockResponse::fixture('report/get_dash_folder2'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $reportApi->createDashboardFolder('PESTPHPDashFolder2');
    $folder = $reportApi->getDashboardFolderByName('PESTPHPDashFolder2');

    expect($folder)->not()->toBe(null);
});

test('it can get a list of dashboards', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('report/list_dashboards'),
    ]);
    $reportApi = getAPI($mockClient)->getReportApi();
    $dashboards = $reportApi->listDashboards();

    // Just verify the API returns an array (scratch org may not have dashboards)
    expect($dashboards)->toBeArray();
});
