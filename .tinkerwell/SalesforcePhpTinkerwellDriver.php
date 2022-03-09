<?php

use myoutdeskllc\SalesforcePhp\SalesforceApi;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\Separator;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\OpenURL;

class SalesforcePhpTinkerwellDriver extends LaravelTinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/src/SalesforceApi.php');
    }

    /**
     * Boot Tinkerwell and load the local .env
     *
     * @param $projectPath
     * @return void
     */
    public function bootstrap($projectPath)
    {
        require $projectPath.'/vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable($projectPath);
        $dotenv->load();
    }

    /**
     * Exposes SalesforceApi as $api to tinkerwell so it's ready to go out of the box
     *
     * @return SalesforceApi[]
     */
    public function getAvailableVariables()
    {
        return [
            'api' => new SalesforceApi(env('TOKEN'), env('INSTANCE_URL'), env('API_VERSION'))
        ];
    }

    /**
     * Return some contextual menu items
     *
     * @return array
     */
    public function contextMenu()
    {
        return [
            Label::create('Salesforce Auth Method: ' . ucfirst(env('AUTH_METHOD'))),
            Label::create('Salesforce API version: ' . env('API_VERSION')),
            OpenURL::create('Salesforce PHP Github', 'https://github.com/MyOutDeskLLC/SalesforcePhp'),
            Submenu::create('Snippets', [
                SetCode::create('Get ReportApi', '$report = SalesforceApi::getReportApi();'),
                SetCode::create('Get ToolingApi', '$toolingApi = SalesforceApi::getToolingApi();'),
                SetCode::create('Get BulkApi', '$bulkApi = SalesforceApi::getBulkApi();'),
                SetCode::create('Get SObjectApi', '$sObjectApi = SalesforceApi::getSObjectApi();'),
                SetCode::create('Get QueryBuilder', '$queryBuilder = SalesforceApi::getQueryBuilder();'),
            ])
        ];
    }
}