<?php

use myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration;
use myoutdeskllc\SalesforcePhp\SalesforceApi;

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (!isset($_ENV['REDIRECT_URI'], $_ENV['SALESFORCE_CONSUMER_KEY'], $_ENV['SALESFORCE_CONSUMER_SECRET'])) {
    echo 'Please make sure you have REDIRECT_URI, SALESFORCE_CONSUMER_KEY, and SALESFORCE_CONSUMER_SECRET set in your .env file.';
    exit;
}

$oauthConfig = OAuthConfiguration::create([
    'client_id'     => $_ENV['SALESFORCE_CONSUMER_KEY'],
    'client_secret' => $_ENV['SALESFORCE_CONSUMER_SECRET'],
    'redirect_uri'  => $_ENV['REDIRECT_URI'],
]);
$salesforceApi = new SalesforceApi();
$salesforceApi->setInstanceUrl($_ENV['SALESFORCE_INSTANCE_URL']);

if (!isset($_GET['code'])) {
    [$url, $state] = array_values($salesforceApi->startOAuthLogin($oauthConfig));

    file_put_contents(__DIR__.'/.state', $state);

    echo "<a class='text-center' href='$url'>Click here to login via OAuth</a>";
} else {
    $state = file_get_contents(__DIR__.'/.state');
    $authenticator = $salesforceApi->completeOAuthLogin($oauthConfig, $_GET['code'], $state);
    $token = $authenticator->getAccessToken();
    $refresh = $authenticator->getRefreshToken();

    file_put_contents('.authenticator', $authenticator->serialize());

    echo '<p>Token is ready, you can use the authenticator by deserializing .authenticator in the root or boot tinkerwell and just use $api.</p>';
}
