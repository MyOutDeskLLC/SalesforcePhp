<?php

use myoutdeskllc\SalesforcePhp\OAuth\SalesforceAuthenticator;
use myoutdeskllc\SalesforcePhp\OAuth\SalesforceOAuthConfiguration;

require('vendor/autoload.php');


function getCredentialsFileLayout()
{
    return [
        'salesforce' => [
            'client_id' => '',
            'secret' => '',
            'redirect_uri' => '',
            'base_uri' => 'https://test.salesforce.com',
            'instance_url' => '',
            'api_version' => 'v42.0'
        ],
        'credentials' => [
            'token' => '',
            'refresh' => ''
        ],
        'direct' => [
            'username' => '',
            'password' => ''
        ]
    ];
}

if(!file_exists('creds.json')) {
    file_put_contents('creds.json', json_encode(getCredentialsFileLayout(), JSON_PRETTY_PRINT));
    echo "Please fill out the salesforce section in creds.json";
    exit(0);
}

$credentials = json_decode(file_get_contents('creds.json'), true);

$sfAuth = new SalesforceOAuthConfiguration();
$sfAuth->setClientId($credentials['salesforce']['client_id']);
$sfAuth->setSecret($credentials['salesforce']['secret']);
$sfAuth->setRedirectUri($credentials['salesforce']['redirect_uri']);
$sfAuth->setBaseUrl($credentials['salesforce']['base_uri']);

$sfAuthenticator = new SalesforceAuthenticator();
$sfAuthenticator->configure($sfAuth);

if(!isset($_GET['code'])) {
    header('Location: ' . $sfAuthenticator->getAuthorizationUrl());
} else {
    $token = $sfAuthenticator->handleAuthorizationCallback($_GET['code']);
    $owner = $sfAuthenticator->getProvider()->getResourceOwner($token);

    $credentials['credentials']['token'] = $token->getToken();
    $credentials['credentials']['refresh'] = $token->getRefreshToken();
    $credentials['salesforce']['instance_url'] = $token->getValues()['instance_url'];

    file_put_contents('creds.json', json_encode($credentials, JSON_PRETTY_PRINT));

    echo 'Token valid for testing';
//    $api = new SalesforceApi($token->getToken(), $credentials['salesforce']['instance_url'], 'v42.0');
//    var_dump($api->listApiVersionsAvailable());
}