<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use InvalidArgumentException;
use myoutdeskllc\SalesforcePhp\Constants\StandardObjectFields;
use myoutdeskllc\SalesforcePhp\QueryBuilder\SoqlQueryBuilder;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\CloneDashboard;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\CreateDashboard;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\CreateFolder;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\CreateReport;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\DeleteDashboard;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\DeleteFolder;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\DeleteReport;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\GetAsyncReportResult;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\GetDashboardMetadata;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\GetDashboardResults;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\GetReportMetadata;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\GetReportTypeMetadata;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\ListReports;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\ListReportTypes;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\QueueReport;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\RunReport;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\SaveReport;
use myoutdeskllc\SalesforcePhp\Requests\Analytics\UpdateDashboard;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Support\SalesforceRules;
use myoutdeskllc\SalesforcePhp\Exceptions\InvalidQueryException;
use Psr\Http\Message\StreamInterface;

class ReportApi extends SalesforceApi
{
    /**
     * Save a report at the given ID with new metadata. If the metadata key is not set, we will set it prior to sending the PATCH request.
     *
     * @param string $id       salesforce id of the report to update
     * @param array  $metadata report metadata (see example)
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_save_report.htm#example_save_report
     */
    public function saveReport(string $id, array $metadata): array
    {
        $request = new SaveReport($id);

        if (!isset($metadata['reportMetadata'])) {
            $metadata = [
                'reportMetadata' => $metadata,
            ];
        }
        $request->body()->set($metadata);

        return $this->executeRequest($request);
    }

    /**
     * Deletes a given report. If this report is in use, this operation will fail.
     *
     * @param string $reportId salesforce id of the report
     *
     * @return bool
     */
    public function deleteReport(string $reportId): bool
    {
        $request = new DeleteReport($reportId);

        return $this->executeRequestDirectly($request)->status() === 204;
    }

    /**
     * Deletes a given dashboard.
     *
     * @param string $dashboardId salesforce id of the dashboard
     *
     * @return bool
     */
    public function deleteDashboard(string $dashboardId): bool
    {
        $request = new DeleteDashboard($dashboardId);

        return $this->executeRequestDirectly($request)->status() === 204;
    }

    /**
     * Runs a report and returns the results in JSON. Runtime metadata MUST contain filters, etc under the reportMetadata key.
     *
     * @param string $id              salesforce id of the report to run
     * @param array  $runtimeMetadata additional filters, user settings for the report
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportdata.htm
     */
    public function runReportSync(string $id, array $runtimeMetadata = [])
    {
        $request = new RunReport($id);
        $request->body()->set($runtimeMetadata);
        $request->query()->set([
            'includeDetails' => true,
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Queues a report to run async. Runtime metadata MUST contain filters, etc under the reportMetadata key. Please store the ID and check again later.
     *
     * @param string $id              salesforce id of the report to queue a run of
     * @param array  $runtimeMetadata additional filters, user settings for the report
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportdata.htm
     */
    public function runReportAsync(string $id, array $runtimeMetadata = [])
    {
        $request = new QueueReport($id);
        $request->body()->set($runtimeMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Returns the results of an async report run. Must be given both the original ID and the queued instance ID from runReportAsync.
     *
     * @param string $originalReportId salesforce id of the original report that was requested
     * @param string $queuedInstanceId salesforce id of the queued instance (was returned in runReportAsync)
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportdata.htm
     */
    public function getAsyncReportResults(string $originalReportId, string $queuedInstanceId)
    {
        $request = new GetAsyncReportResult($originalReportId, $queuedInstanceId);

        return $this->executeRequest($request);
    }

    /**
     * Downloads a report into an excel format. This returns a stream for sending to the client with the proper header.
     *
     * @param string $id ID of the report to export
     *
     * @return StreamInterface
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_download_excel.htm
     */
    public function downloadReport(string $id): StreamInterface
    {
        $request = new RunReport($id);
        $request->headers()->merge([
            'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        return $this->executeRequestDirectly($request)->stream();
    }

    /**
     * List recently viewed reports.
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_list_recentreports.htm
     */
    public function listRecentlyViewedReports(): array
    {
        $request = new ListReports();

        return $this->executeRequest($request);
    }

    /**
     * List report types available (these are custom or built in report types).
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_reporttypes_example_get_reporttypes.htm
     */
    public function listReportTypes(): array
    {
        $request = new ListReportTypes();

        return $this->executeRequest($request);
    }

    /**
     * Gets metadata for a specific report type. This typeName should be the "type" field returned from listReportTypes.
     *
     * @param string $typeName type of report. Not to be confused with Dashobards vs. reports, these are custom types in Setup -> Report Types.
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_reporttypes_reference_reporttype.htm
     */
    public function getReportTypeMetadata(string $typeName): array
    {
        $request = new GetReportTypeMetadata($typeName);

        return $this->executeRequest($request);
    }

    /**
     * Return a list of reports that the querying user has access to.
     *
     * @param array $additionalSelects additional fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listReports(array $additionalSelects = []): array
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName', 'OwnerId'], $additionalSelects);
        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report');

        return $this->executeQuery($builder);
    }

    /**
     * Return a list of reports that the querying user has access to. May return odd results if two reports have the same exact name.
     *
     * @param string $reportName        name of the report
     * @param array  $additionalSelects additional fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array|null
     */
    public function getReportByName(string $reportName, array $additionalSelects = []): ?array
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName', 'OwnerId'], $additionalSelects);
        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report')
            ->where('name', '=', $reportName)
            ->limit(1);

        $reportFound = $this->executeQuery($builder);
        if ((isset($reportFound['totalSize']) && $reportFound['totalSize'] === 0) || empty($reportFound)) {
            // TODO: throw not found exception?
            return null;
        }

        return $reportFound[0];
    }

    /**
     * Returns dashboard information (via soql). Use metadata call for more information.
     *
     * @param string $dashboardName     name of the dashboard
     * @param array  $additionalSelects additional fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array|null
     */
    public function getDashboardByName(string $dashboardName, array $additionalSelects = []): ?array
    {
        $select = array_merge(['Id', 'Title', 'DeveloperName', 'FolderName'], $additionalSelects);
        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Dashboard')
            ->where('Title', '=', $dashboardName)
            ->limit(1);

        $dashboard = $this->executeQuery($builder);
        if ((isset($dashboard['totalSize']) && $dashboard['totalSize'] === 0) || empty($dashboard)) {
            // TODO: throw not found exception?
            return null;
        }

        return $dashboard[0];
    }

    /**
     * Lists folders (using soql).
     *
     * @param array $additionalSelects additional fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listFolders(array $additionalSelects = []): array
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'Type', 'AccessType', 'NamespacePrefix'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Folder');

        return $this->executeQuery($builder);
    }

    /**
     * Returns a folder by name, but this one is set with the type to Dashboard as SF treats them differently.
     *
     * @param string $folderName        folder name to query
     * @param array  $additionalSelects fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function getDashboardFolderByName(string $folderName, array $additionalSelects = []): array
    {
        return $this->getFolderByName($folderName, 'Dashboard', $additionalSelects);
    }

    /**
     * Finds the folder with the given name.
     *
     * @param string $folderName        folder name to query
     * @param string $type              the type of folder (dashboard or report)
     * @param array  $additionalSelects fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array|null
     */
    public function getFolderByName(string $folderName, string $type = 'Report', array $additionalSelects = []): ?array
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'Type', 'AccessType', 'NamespacePrefix'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Folder')
            ->where('Name', '=', $folderName)
            ->where('type', '=', $type)
            ->limit(1);

        $foldersFound = $this->executeQuery($builder);
        if ((isset($foldersFound['totalSize']) && $foldersFound['totalSize'] === 0) || empty($foldersFound)) {
            // TODO: throw not found exception?
            return null;
        }

        return $foldersFound[0];
    }

    /**
     * Returns reports in the target folder name.
     *
     * @param string $folderName        folder name to query
     * @param array  $additionalSelects fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listReportsInFolderByName(string $folderName, array $additionalSelects = []): array
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report')
            ->where('FolderName', '=', $folderName);

        return $this->executeQuery($builder);
    }

    /**
     * Returns dashboards in a given folder.
     *
     * @param string $folderName        folder name to query
     * @param array  $additionalSelects fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listDashboardsInFolderByName(string $folderName, array $additionalSelects = []): array
    {
        $select = array_merge(['Id', 'Title', 'DeveloperName', 'FolderName'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Dashboard')
            ->where('FolderName', '=', $folderName);

        return $this->executeQuery($builder);
    }

    /**
     * Returns dashboards in a given folder.
     *
     * @param string $folderId          salesforce id of the folder
     * @param array  $additionalSelects fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listDashboardsInFolderById(string $folderId, array $additionalSelects = []): array
    {
        $select = array_merge(['Id', 'Title', 'DeveloperName', 'FolderName', 'FolderId'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Dashboard')
            ->where('FolderId', '=', $folderId);

        return $this->executeQuery($builder);
    }

    /**
     * According to the docs, the "OwnerId" is not the owner but rather the folder for reports.
     *
     * @param string $folderId          salesforce id of the folder
     * @param array  $additionalSelects fields to select
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listReportsInFolderById(string $folderId, array $additionalSelects = []): array
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName', 'OwnerId'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report')
            ->where('OwnerId', '=', $folderId);

        return $this->executeQuery($builder);
    }

    /**
     * Copies a report to the same folder.
     *
     * @param string $reportId      salesforce id of the report
     * @param string $newReportName name of the report
     *
     * @return array
     */
    public function copyReportToSameFolder(string $reportId, string $newReportName): array
    {
        if (empty($newReportName) || strlen($newReportName) >= 40) {
            throw new InvalidArgumentException('Report name invalid. Must be between 1-40 characters');
        }
        $existingMetadata = $this->getReportMetadata($reportId);
        $metadata = $existingMetadata['reportMetadata'];
        $metadata['name'] = $newReportName;
        unset($metadata['id'], $metadata['developerName'], $metadata['attributes']);

        return $this->createReport($metadata);
    }

    /**
     * Returns report metadata.
     *
     * @param string $id salesforce id of the report to query
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportmetadata.htm
     */
    public function getReportMetadata(string $id): array
    {
        $request = new GetReportMetadata($id);

        return $this->executeRequest($request);
    }

    /**
     * Saves a new report with the given metadata.
     *
     * @param array $reportMetadata additional filters, user settings for the report
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_save_report.htm#example_save_report
     */
    public function createReport(array $reportMetadata): array
    {
        $request = new CreateReport();

        if (!isset($reportMetadata['reportMetadata'])) {
            $reportMetadata = [
                'reportMetadata' => $reportMetadata,
            ];
        }
        $request->body()->set($reportMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Copies a report to a new folder, with an updated name.
     *
     * @param string $reportId      salesforce id of the report
     * @param string $newReportName the new name of the report
     * @param string $folderId      salesforce id of the target folder
     *
     * @return array
     */
    public function copyReportToNewFolder(string $reportId, string $newReportName, string $folderId): array
    {
        if (empty($newReportName) || strlen($newReportName) >= 40) {
            throw new InvalidArgumentException('Report name invalid. Must be between 1-40 characters');
        }
        $existingMetadata = $this->getReportMetadata($reportId);
        $metadata = $existingMetadata['reportMetadata'];
        $metadata['name'] = $newReportName;
        $metadata['folderId'] = $folderId;
        unset($metadata['Id'], $metadata['developerName'], $metadata['attributes']);

        return $this->createReport($metadata);
    }

    /**
     * Uses SOQL to query for a list of Dashboards available to the user.
     *
     * @throws InvalidQueryException
     *
     * @return array
     */
    public function listDashboards(): array
    {
        $builder = (new SoqlQueryBuilder())
            ->select(StandardObjectFields::DASHBOARD_FIELDS)
            ->from('Dashboard');

        return $this->executeQuery($builder);
    }

    /**
     * Get dashboard metadata from the analytics API.
     *
     * @param string $dashboardId salesforce if of the dashboard
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_dashboard_example_get_dashboard_metadata.htm
     */
    public function getDashboardMetadata(string $dashboardId): array
    {
        $request = new GetDashboardMetadata($dashboardId);

        return $this->executeRequestDirectly($request)->json();
    }

    /**
     * Returns metadata, data, and status for the specified dashboard.
     *
     * @param string $dashboardId salesforce id of the dashboard
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_results_resource.htm
     */
    public function getDashboardResults(string $dashboardId): array
    {
        $request = new GetDashboardResults($dashboardId);

        return $this->executeRequestDirectly($request)->json();
    }

    /**
     * Creates a dashboard with the given metadata.
     *
     * @param array $dashboardMetadata dashboard metadata (see example)
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_results_resource.htm
     */
    public function createDashboard(array $dashboardMetadata): array
    {
        $request = new CreateDashboard();
        $request->body()->set($dashboardMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Clones a dashboard.
     *
     * @param string $dashboardId salesforce id of the dashboard
     * @param string $newFolderId salesforce id of the folder
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_clone_dashboard.htm
     */
    public function cloneDashboard(string $dashboardId, string $newFolderId): array
    {
        $request = new CloneDashboard($dashboardId, $newFolderId);

        return $this->executeRequest($request);
    }

    /**
     * Updates an existing dashboard with new metadata.
     *
     * @param string $dashboardId       salesforce id of the dashboard
     * @param array  $dashboardMetadata dashboard metadata (see example)
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_save_dashboard.htm#topic-title
     */
    public function updateDashboard(string $dashboardId, array $dashboardMetadata): array
    {
        $request = new UpdateDashboard($dashboardId);
        $request->body()->set($dashboardMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Return metadata information about components on a dashboard.
     *
     * @param string $dashboardId  salesforce id of the dashboard
     * @param array  $componentIds salesforce id of the components to fetch, in an array
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_example_return_details_about_dashboard_components.htm
     */
    public function getDashboardComponentDetails(string $dashboardId, array $componentIds): array
    {
        $request = new GetDashboardResults($dashboardId);
        $request->body()->set([
            'componentIds' => $componentIds,
        ]);

        return $this->executeRequestDirectly($request)->json();
    }

    /**
     * Creates a folder with the given name.
     *
     * @param string $folderName The Folder API Name can only contain underscores and alphanumeric characters. It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_folders_create.htm
     */
    public function createReportFolder(string $folderName): array
    {
        $request = new CreateFolder();
        $request->body()->set([
            'label' => $folderName,
            'name'  => $this->prepareFolderApiName($folderName),
            'type'  => 'report',
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Prepares an API name on behalf of the user.
     *
     * @param $folderName
     *
     * @return string
     */
    private function prepareFolderApiName($folderName): string
    {
        $apiName = str_replace(' ', '_', $folderName);

        $folderRegex = SalesforceRules::getFolderNameValidation();
        if (preg_match($folderRegex, $apiName) === 0) {
            throw new InvalidArgumentException('The Folder API Name can only contain underscores and alphanumeric characters. It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.');
        }

        return $apiName;
    }

    /**
     * Creates a dashboard folder with the given name.
     *
     * @param string $folderName The Folder API Name can only contain underscores and alphanumeric characters. It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_folders_create.htm
     */
    public function createDashboardFolder(string $folderName): array
    {
        $request = new CreateFolder();
        $request->body()->set([
            'label' => $folderName,
            'name'  => $this->prepareFolderApiName($folderName),
            'type'  => 'dashboard',
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Delete a folder with the given ID.
     *
     * @param string $folderId salesforce id of the folder
     *
     * @return bool
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_folders_create.htm
     */
    public function deleteFolder(string $folderId): bool
    {
        $request = new DeleteFolder($folderId);

        $response = $this->executeRequestDirectly($request);

        return $response->status() === 204;
    }
}
