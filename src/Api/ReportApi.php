<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use myoutdeskllc\SalesforcePhp\Constants\StandardObjectFields;
use myoutdeskllc\SalesforcePhp\Helpers\SoqlQueryBuilder;
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

class ReportApi extends SalesforceApi
{
    /**
     * Returns report metadata
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportmetadata.htm
     */
    public function getReportMetadata(string $id)
    {
        $request = new GetReportMetadata($id);

        return $this->executeRequest($request);
    }

    /**
     * Save a report at the given ID with new metadata. If the metadata key is not set, we will set it prior to sending the PATCH request
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_save_report.htm#example_save_report
     */
    public function saveReport(string $id, array $metadata)
    {
        $request = new SaveReport($id);

        if(!isset($metadata['reportMetadata'])) {
            $metadata = [
                'reportMetadata' => $metadata
            ];
        }
        $request->setData($metadata);

        return $this->executeRequest($request);
    }

    /**
     * Saves a new report with the given metadata
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_save_report.htm#example_save_report
     */
    public function createReport(array $reportMetadata)
    {
        $request = new CreateReport();

        if(!isset($metadata['reportMetadata'])) {
            $reportMetadata = [
                'reportMetadata' => $reportMetadata
            ];
        }
        $request->setData($reportMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Deletes a given report. If this report is in use, this operation will fail.
     */
    public function deleteReport(string $reportId)
    {
        $request = new DeleteReport($reportId);

        return $this->executeRequest($request);
    }

    /**
     * Deletes a given dashboard
     */
    public function deleteDashboard(string $dashboardId)
    {
        $request = new DeleteDashboard($dashboardId);

        return $this->executeRequest($request);
    }

    /**
     * Runs a report and returns the results in JSON. Runtime metadata MUST contain filters, etc under the reportMetadata key
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportdata.htm
     */
    public function runReportSync(string $id, array $runtimeMetadata = [])
    {
        $request = new RunReport($id);
        $request->setData($runtimeMetadata);
        $request->mergeQuery([
            'includeDetails' => true
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Queues a report to run async. Runtime metadata MUST contain filters, etc under the reportMetadata key. Please store the ID and check again later
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportdata.htm
     */
    public function runReportAsync(string $id, array $runtimeMetadata = [])
    {
        $request = new QueueReport($id);
        $request->setData($runtimeMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Returns the results of an async report run. Must be given both the original ID and the queued instance ID from runReportAsync
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_get_reportdata.htm
     */
    public function getAsyncReportResults(string $originalReportId, string $queuedInstanceId)
    {
        $request = new GetAsyncReportResult($originalReportId, $queuedInstanceId);

        return $this->executeRequest($request);
    }

    /**
     * Downloads a report into an excel format. This returns a stream for sending to the client with the proper header
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_download_excel.htm
     */
    public function downloadReport(string $id): \Psr\Http\Message\StreamInterface
    {
        $request = new RunReport($id);
        $request->mergeHeaders([
            'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);

        return $request->send()->getStream();
    }

    /**
     * List recently viewed reports
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_list_recentreports.htm
     */
    public function listRecentlyViewedReports()
    {
        $request = new ListReports();

        return $this->executeRequest($request);
    }

    /**
     * List report types available (these are custom or built in report types)
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_reporttypes_example_get_reporttypes.htm
     */
    public function listReportTypes()
    {
        $request = new ListReportTypes();

        return $this->executeRequest($request);
    }

    /**
     * Gets metadata for a specific report type. This typeName should be the "type" field returned from listReportTypes
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_reporttypes_reference_reporttype.htm
     */
    public function getReportTypeMetadata(string $typeName)
    {
        $request = new GetReportTypeMetadata($typeName);

        return $this->executeRequest($request);
    }

    /**
     * Return a list of reports that the querying user has access to
     */
    public function listReports(array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName', 'OwnerId'], $additionalSelects);
        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report');

        return $this->executeQuery($builder);
    }

    /**
     * Return a list of reports that the querying user has access to
     */
    public function getReportByName(string $reportName, array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName', 'OwnerId'], $additionalSelects);
        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report')
            ->where('name', '=', $reportName)
            ->limit(1);

        $reportFound = $this->executeQuery($builder);
        if((isset($reportFound['totalSize']) && $reportFound['totalSize'] === 0) || empty($reportFound)) {
            // TODO: throw not found exception?
            return null;
        }

        return $reportFound[0];
    }

    /**
     * Returns dashboard information (via soql). Use metadata call for more information
     */
    public function getDashboardByName(string $dashboardName, array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Title', 'DeveloperName', 'FolderName'], $additionalSelects);
        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Dashboard')
            ->where('Title', '=', $dashboardName)
            ->limit(1);

        $dashboard = $this->executeQuery($builder);
        if((isset($dashboard['totalSize']) && $dashboard['totalSize'] === 0) || empty($dashboard)) {
            // TODO: throw not found exception?
            return null;
        }

        return $dashboard[0];
    }

    /**
     * There is an endpoint for this on API version 41.0, but it did not seem to work properly and instead said http methods were not allowed
     */
    public function listFolders(array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'Type', 'AccessType', 'NamespacePrefix'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Folder');

        return $this->executeQuery($builder);
    }

    /**
     * Finds the folder with the given name
     */
    public function getFolderByName(string $folderName, string $type = 'Report', array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'Type', 'AccessType', 'NamespacePrefix'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Folder')
            ->where('Name', '=', $folderName)
            ->where('type', '=', $type)
            ->limit(1);

        $foldersFound = $this->executeQuery($builder);
        if((isset($foldersFound['totalSize']) && $foldersFound['totalSize'] === 0) || empty($foldersFound)) {
            // TODO: throw not found exception?
            return null;
        }

        return $foldersFound[0];
    }

    /**
     * Returns a folder by name, but this one is set with the type to Dashboard as SF treats them differently
     */
    public function getDashboardFolderByName(string $folderName, array $additionalSelects = [])
    {
        return $this->getFolderByName($folderName, 'Dashboard', $additionalSelects);
    }

    /**
     * Returns reports in the target folder name
     */
    public function listReportsInFolderByName(string $folderName, array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report')
            ->where('FolderName', '=', $folderName);

        return $this->executeQuery($builder);
    }

    /**
     * Returns dashboards in a given folder
     */
    public function listDashboardsInFolderByName(string $folderName, array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Title', 'DeveloperName', 'FolderName'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Dashboard')
            ->where('FolderName', '=', $folderName);

        return $this->executeQuery($builder);
    }

    /**
     * Returns dashboards in a given folder
     */
    public function listDashboardsInFolderById(string $folderId, array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Title', 'DeveloperName', 'FolderName', 'FolderId'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Dashboard')
            ->where('FolderId', '=', $folderId);

        return $this->executeQuery($builder);
    }

    /**
     * According to the docs, the "OwnerId" is not the owner but rather the folder for reports
     */
    public function listReportsInFolderById(string $folderId, array $additionalSelects = [])
    {
        $select = array_merge(['Id', 'Name', 'DeveloperName', 'FolderName', 'OwnerId'], $additionalSelects);

        $builder = (new SoqlQueryBuilder())
            ->select($select)
            ->from('Report')
            ->where('OwnerId', '=', $folderId);

        return $this->executeQuery($builder);
    }

    /**
     * Copies a report to the same folder
     */
    public function copyReportToSameFolder(string $reportId, string $newReportName)
    {
        if(empty($newReportName) || strlen($newReportName) >= 40) {
            throw new \InvalidArgumentException('Report name invalid. Must be between 1-40 characters');
        }
        $existingMetadata = $this->getReportMetadata($reportId);
        $metadata = $existingMetadata['reportMetadata'];
        $metadata['name'] = $newReportName;
        unset($metadata['id'], $metadata['developerName'], $metadata['attributes']);

        return $this->createReport($metadata);
    }

    /**
     * Copies a report to a new folder, with an updated name
     */
    public function copyReportToNewFolder(string $reportId, string $newReportName, string $folderId)
    {
        if(empty($newReportName) || strlen($newReportName) >= 40) {
            throw new \InvalidArgumentException('Report name invalid. Must be between 1-40 characters');
        }
        $existingMetadata = $this->getReportMetadata($reportId);
        $metadata = $existingMetadata['reportMetadata'];
        $metadata['name'] = $newReportName;
        $metadata['folderId'] = $folderId;
        unset($metadata['Id'], $metadata['developerName'], $metadata['attributes']);

        return $this->createReport($metadata);
    }

    /**
     * Uses SOQL to query for a list of Dashboards available to the user
     */
    public function listDashboards()
    {
        $builder = (new SoqlQueryBuilder())
            ->select(StandardObjectFields::DASHBOARD_FIELDS)
            ->from('Dashboard');

        return $this->executeQuery($builder);
    }

    /**
     * Get dashboard metadata from the analytics API
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/analytics_api_dashboard_example_get_dashboard_metadata.htm
     */
    public function getDashboardMetadata(string $dashboardId)
    {
        $request = new GetDashboardMetadata($dashboardId);

        return $this->executeRequest($request);
    }

    /**
     * 	Returns metadata, data, and status for the specified dashboard
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_results_resource.htm
     */
    public function getDashboardResults(string $dashboardId)
    {
        $request = new GetDashboardResults($dashboardId);

        return $this->executeRequest($request);
    }

    /**
     * Creates a dashboard with the given metadata
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_results_resource.htm
     */
    public function createDashboard(array $dashboardMetadata)
    {
        $request = new CreateDashboard();
        $request->setData($dashboardMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Clones a dashboard
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.234.0.api_analytics.meta/api_analytics/sforce_analytics_rest_api_clone_dashboard.htm
     */
    public function cloneDashboard(string $dashboardId, string $newFolderId)
    {
        $request = new CloneDashboard($dashboardId, $newFolderId);

        return $this->executeRequest($request);
    }

    /**
     * Updates an existing dashboard with new metadata.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_save_dashboard.htm#topic-title
     */
    public function updateDashboard(string $dashboardId, array $dashboardMetadata)
    {
        $request = new UpdateDashboard($dashboardId);
        $request->setData($dashboardMetadata);

        return $this->executeRequest($request);
    }

    /**
     * Return metadata information about components on a dashboard
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_dashboard_example_return_details_about_dashboard_components.htm
     */
    public function getDashboardComponentDetails(string $dashboardId, array $componentIds)
    {
        $request = new GetDashboardResults($dashboardId);
        $request->setData([
            'componentIds' => $componentIds
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Creates a folder with the given name
     *
     * The Folder API Name can only contain underscores and alphanumeric characters. It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_folders_create.htm
     */
    public function createReportFolder(string $folderName)
    {
        $request = new CreateFolder();
        $request->setData([
            'label' => $folderName,
            'name' => $this->prepareFolderApiName($folderName),
            'type' => 'report'
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Creates a dashboard folder with the given name
     *
     * The Folder API Name can only contain underscores and alphanumeric characters. It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_folders_create.htm
     */
    public function createDashboardFolder(string $folderName)
    {
        $request = new CreateFolder();
        $request->setData([
            'label' => $folderName,
            'name' => $this->prepareFolderApiName($folderName),
            'type' => 'dashboard'
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Delete a folder with the given ID.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_analytics.meta/api_analytics/analytics_api_folders_create.htm
     */
    public function deleteFolder(string $folderId)
    {
        $request = new DeleteFolder($folderId);

        return $this->executeRequest($request);
    }

    /**
     * Prepares an API name on behalf of the user
     */
    private function prepareFolderApiName($folderName) : string
    {
        $apiName = str_replace(' ', '_', $folderName);

        $folderRegex = '/^([a-zA-Z])(?!\w*__)\w+?\w*(?<!_)$/m';
        if(preg_match($folderRegex, $apiName) === 0) {
            throw new \InvalidArgumentException('The Folder API Name can only contain underscores and alphanumeric characters. It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.');
        }

        return $apiName;
    }
}