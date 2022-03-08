<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

use myoutdeskllc\SalesforcePhp\SalesforceApi;

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getAPI()
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
    $dotenv->load();

    return new SalesforceApi(env('TOKEN'), env('INSTANCE_URL'), env('API_VERSION'));
}

function toFlatArray(array $results, string $key)
{
    return array_map(function ($result) use ($key) {
        return $result[$key];
    }, $results);
}

function destroyPestPhpSalesforceChanges()
{
    $reportApi = SalesforceApi::getReportApi();
    $reports = $reportApi->listReports();
    $folders = $reportApi->listFolders();

    foreach ($reports as $report) {
        if (str_starts_with($report['Name'], 'PESTPHP')) {
            $reportApi->deleteReport($report['Id']);
        }
    }
    foreach ($folders as $folder) {
        if (str_starts_with($folder['Name'], 'PESTPHP')) {
            if ($folder['Type'] === 'Dashboard') {
                $dashboardsInFolder = $reportApi->recordsOnly()->listDashboardsInFolderById($folder['Id']);
                foreach ($dashboardsInFolder as $dashboard) {
                    $reportApi->deleteDashboard($dashboard['Id']);
                }
            }

            try {
                $reportApi->deleteFolder($folder['Id']);
            } catch (\Exception $e) {
                // eat this error. Sometimes deletions just fail due to needing a hard delete from the bulk api
            }
        }
    }
}
