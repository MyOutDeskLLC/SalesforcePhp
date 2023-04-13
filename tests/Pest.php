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

    $api = new SalesforceApi($_ENV['SALESFORCE_INSTANCE_URL'], $_ENV['API_VERSION']);

    // this is questionable, but works for testing with OAuth connections
    $api->restoreExistingOAuthConnection(file_get_contents('.authenticator'), function ($authenticator) {
        file_put_contents('.authenticator', $authenticator->serialize());
    });

    $api->recordsOnly();

    return $api;
}

function toFlatArray(array $results, string $key)
{
    return array_map(function ($result) use ($key) {
        return $result[$key];
    }, $results);
}

function destroyPestPhpSalesforceChanges()
{
    $reportApi = getAPI()->recordsOnly()->getReportApi();

    $reports = $reportApi->listReports();
    $folders = $reportApi->listFolders();

    foreach ($reports as $report) {
        if (stripos($report['Name'], 'PESTPHP') !== false) {
            $reportApi->deleteReport($report['Id']);
        }
    }

    foreach ($folders as $folder) {
        if (stripos($folder['Name'], 'PESTPHP') !== false) {
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
