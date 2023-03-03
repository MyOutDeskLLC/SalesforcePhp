<?php

beforeEach(function () {
    getAPI();
});

afterAll(function () {
    destroyPestPhpSalesforceChanges();
});

test('it can query folders', function () {
    $reportApi = getAPI()->getReportApi();
    $foldersAvailable = toFlatArray($reportApi->listFolders(), 'Name');

    expect($foldersAvailable)->toContain("Sakura's Reports");
});

test('it can create report folder(s)', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createReportFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getFolderByName("Sakura\'s Reports");

    expect($folder)->not()->toBeNull();
});

test('it can create dashboard folder(s)', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createDashboardFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getFolderByName("Sakura\'s Reports");

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
    $reportApi = getAPI()->getReportApi();
    $foldersAvailable = $reportApi->recordsOnly()->getFolderByName("Sakura\'s Reports");

    expect($foldersAvailable)->toHaveKey('Name', "Sakura's Reports");
});

test('it returns null when folders are not found', function () {
    $reportApi = getAPI()->getReportApi();
    $foldersAvailable = $reportApi->recordsOnly()->getFolderByName('This is not a real folder');

    expect($foldersAvailable)->toBeNull();
});

test('it can find reports', function () {
    $reportApi = getAPI()->getReportApi();
    $reports = toFlatArray($reportApi->recordsOnly()->listReports(), 'Name');

    expect($reports)->toContain('Account Test');
});

test('it can find reports by name', function () {
    $reportApi = getAPI()->getReportApi();
    $report = $reportApi->recordsOnly()->getReportByName('Account Test');

    expect($report)->toHaveKey('Name', 'Account Test');
});

test('it returns null when a report cannot be found', function () {
    $reportApi = getAPI()->getReportApi();
    $report = $reportApi->recordsOnly()->getReportByName('Games Done Quick 2020');

    expect($report)->toBeNull();
});

test('it can copy a report to the same folder (Integration Tests folder)', function () {
    $temporaryName = 'PESTPHP - '.bin2hex(random_bytes(10));

    $reportApi = getAPI()->getReportApi();
    $report = $reportApi->recordsOnly()->getReportByName('Account Test');
    $reportApi->copyReportToSameFolder($report['Id'], $temporaryName);
    $reportJustCreated = $reportApi->recordsOnly()->getReportByName($temporaryName);

    expect($reportJustCreated)->not()->toBeNull();
});

test('it can copy a report to a new folder (Integration Test -> Sakura\'s Folder)', function () {
    $temporaryName = 'PESTPHP - '.bin2hex(random_bytes(10));

    $reportApi = getAPI()->getReportApi();
    $report = $reportApi->recordsOnly()->getReportByName('Account Test');
    $sakuraFolder = $reportApi->recordsOnly()->getFolderByName("Sakura\'s Reports");

    $reportApi->copyReportToNewFolder($report['Id'], $temporaryName, $sakuraFolder['Id']);
    $reportJustCreated = $reportApi->recordsOnly()->getReportByName($temporaryName);

    expect($reportJustCreated)->not()->toBeNull();
    expect($reportJustCreated['FolderName'])->toEqual('Sakura\'s Reports');
});

test('it can get a list of dashboards', function () {
    $reportApi = getAPI()->getReportApi();
    $dashboards = toFlatArray($reportApi->recordsOnly()->listDashboards(), 'Title');

    expect($dashboards)->toContain('Elite Dashboard');
});

test('it can create a folder for dashboards', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createDashboardFolder($temporaryName);
    $folder = $reportApi->recordsOnly()->getDashboardFolderByName($temporaryName);

    expect($folder)->not()->toBe(null);
});

test('it can clone a dashboard into a new folder', function () {
    $temporaryName = 'PESTPHP'.bin2hex(random_bytes(10));
    $reportApi = getAPI()->getReportApi();
    $reportApi->createDashboardFolder($temporaryName);

    $folder = $reportApi->getDashboardFolderByName($temporaryName);
    $existingDashboard = $reportApi->getDashboardByName('Elite Dashboard');
    $reportApi->cloneDashboard($existingDashboard['Id'], $folder['Id']);

    $createdDashboards = $reportApi->listDashboardsInFolderByName($temporaryName);

    expect($createdDashboards)->not()->toBeEmpty();
});

test('it can query dashboard results', function () {
    $reportApi = getAPI()->getReportApi();
    $existingDashboard = $reportApi->recordsOnly()->getDashboardByName('Elite Dashboard');
    $existingDashboard = $reportApi->getDashboardResults($existingDashboard['Id']);

    // this may not be refreshed unless you login and visually look at it, so we'll just check that component data is not empty
    expect($existingDashboard['componentData'])->not()->toBeEmpty();
});

test('it can run reports asynchronously', function () {
    $reportApi = getAPI()->getReportApi();
    $report = $reportApi->recordsOnly()->getReportByName('Account Test');
    $asyncRun = $reportApi->runReportAsync($report['Id']);
    sleep(2);
    $finalResults = $reportApi->getAsyncReportResults($report['Id'], $asyncRun['id']);

    expect($finalResults['factMap'])->not()->toBeEmpty();
});
