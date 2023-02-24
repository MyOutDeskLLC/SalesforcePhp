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
            'api' =>(function() : SalesforceApi {
                $api = new SalesforceApi($_ENV['SALESFORCE_INSTANCE_URL']);
                $api->restoreExistingOAuthConnection((file_get_contents('.authenticator')), function($authenticator) {
                    file_put_contents('.authenticator', $authenticator->serialize());
                });
                return $api;
            })()
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
        ];
    }
}