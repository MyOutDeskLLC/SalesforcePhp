<?php

use myoutdeskllc\SalesforcePhp\OAuth\SalesforceApiUserLogin;
use myoutdeskllc\SalesforcePhp\OAuth\SalesforceAuthenticator;
use myoutdeskllc\SalesforcePhp\OAuth\SalesforceOAuthConfiguration;

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (env('AUTH_METHOD') === 'oauth') {
    $sfAuth = new SalesforceOAuthConfiguration();
    $sfAuth->setClientId(env('CLIENT_ID'));
    $sfAuth->setSecret(env('CLIENT_SECRET'));
    $sfAuth->setRedirectUri(env('REDIRECT_URL'));
    $sfAuth->setBaseUrl(env('BASE_URL'));

    $sfAuthenticator = new SalesforceAuthenticator();
    $sfAuthenticator->configure($sfAuth);
    if (!isset($_GET['code'])) {
        header('Location: '.$sfAuthenticator->getAuthorizationUrl());
    } else {
        $token = $sfAuthenticator->handleAuthorizationCallback($_GET['code']);
        $owner = $sfAuthenticator->getProvider()->getResourceOwner($token);

        echo '<p>Please place these into your .env</p>';
        echo "<p>Token: {$token->getToken()}</p><p>Refresh Token: {$token->getRefreshToken()}</p><p>Instance URL: {$token->getValues()['instance_url']}</p>";
    }
} else {
    $apiLogin = new SalesforceApiUserLogin();
    $apiLogin->configureApp(env('CLIENT_ID'), env('CLIENT_SECRET'));
    $result = $apiLogin->authenticateUser(env('USERNAME'), env('PASSWORD'));
    echo '<p>Please place these into your .env</p>';
    echo "<p>Token: {$result['access_token']}</p>";
    echo "<p>Instance URL: {$result['instance_url']}</p>";
}
