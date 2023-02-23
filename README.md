# Salesforce PHP API (Alpha)
A beautiful, extendable API powered by [Saloon](https://github.com/sammyjo20/saloon).

![img](https://github.styleci.io/repos/467300822/shield)
![MyOutDeskLLC](https://circleci.com/gh/MyOutDeskLLC/SalesforcePhp.svg?style=shield)

## Introduction
The goal of this API was to create a feature rich, elegant baseline for working with the Salesforce API. Developers can 
install and leverage this API to help them integrate Salesforce easily and beautifully. These endpoints return salesforce
responses, and most of the endpoints contain links to specific documentation.

### Why?
While we did have the original [SalesforceRest](https://github.com/MyOutDeskLLC/SalesforceRest) library, there were several issues we ran into which were not practical with it. The gist of what spawned this library was the following needs:

- Unstable testing due to the fact every SF org is different
- Bulk API Uploads were messy
- Ability to query field api names without digging into salesforce, opening fields with similiar names, etc
- Ability to query chunks of data, by time frame, which matches expected data in salesforce (Soql Date Constants)
- A nicer way to build queries due to soql conventions which are similiar but not 1:1 with typical sql

And some other local needs by our team:
- Faster way to copy paste templated reports to new team members
- Copying & updating dashboads for specific team members, which are linked to said reports

## Authors
- [JL](https://github.com/WalrusSoup)

## Features
There are many out of the box features ready for you to build upon. Please consult the [tests](/tests/) folder for specific scenarios.

- Login Support
  - OAuth
  - Username \ Password Flow (API Users)
- Basic Operations
  - Query record(s)
  - Insert record(s)
  - Supports decorated methods for popular common objects
    - Leads
    - Accounts
    - Opportunities
    - Contacts
  - [Search Helpers](/tests/SearchTest.php)
- SObject Metadata API
  - Query basic information
  - Query full metadata (fields, etc)
- Bulk API 2.0
  - Create and manage bulk api 2.0 jobs
  - Several upload methods supported
    - CSV via filename
    - CSV via file stream
    - Pass in records as an array
  - Includes SalesforceJob class to help manage operations
    - This is optional and direct methods are also exposed
- Run SOQL Queries
  - Access [SOQL Query Builder](https://github.com/mihasicehcek/php-salesforce-soql-builder)
  - Helper functions for [SOQL date constants](/src/Constants/SoqlDates.php)
- Analytics API (Reports, Dashboards)
  - List, Query Basic Information, Metadata for Reports, Dashboards
  - List, Create, Update, Delete, Search for report and dashboard folders 
  - Create, Manage, Update, Delete, Copy Reports 
  - Create, Manage, Update, Delete, Copy Dashboards
  - Run Dashboards, Reports & Get Results Synch, Asynch
- Tooling API
  - Get information on apex classes, apex pages
  - List, Download Apex Logs
  - List previous runs, run tests over REST (sync, async)
  - Execute anonymous apex
  - List, Get, Delete Email Templates
    - Creating email templates is horrific. You can, but don't bother.
- Organization
  - Supported API versions
  - Limits
- Tinkerwell Support

## Implementing
If you need to add specific low level operations, please submit a PR to this library. Most cases, however, should be available via
the basic createRecord operation.

```php
private SalesforceApi $salesforceApi;

public function addMySalesforceObject() 
{
    return $this->salesforceApi->createRecord('My_Record__c', [
        'Name' => 'Test',
        'Status__c' => 'Online'
    ]);
}

public function getMySalesforceObject(string $id)
{
    return $this->salesforceApi->getRecord('My_Record__c', $id, ['Name','Status__c']);
}
```

## Tinkerwell Support
Once configured, this project supports [Tinkerwell](https://tinkerwell.app/) out of the box with the data supplied in your `.env`. Opening the working directory in Tinkerwell will have snippets and automatically make the api for you to use as `$api`. This will help you test and script the project, or simply interact with your own Salesforce instance.

![tinkerwell_support](https://user-images.githubusercontent.com/5719851/157559447-c4513e0c-7da2-48e7-8971-e200e95b4afd.png)



## Soql Builder
The SOQL builder can help build out queries in your app more effectively. You'll want to make sure security is tight before
it hits the builder, but it offers a fluent API to help build out queries.

```php
$builder = SalesforceApi::getQueryBuilder();

$builder
    ->select(['Id', 'Name', 'created_at'])
    ->from('Account')
    ->where('Name', '=', 'Test')
    ->limit(20)
    ->orderBy('created_at', 'DESC')
    ->toSoql();
```
**output:**

`> SELECT Id, Name, created_at FROM Account WHERE Name = 'Test' ORDER BY created_at DESC LIMIT 20`
## Testing
Testing is done via PestPHP. To ensure full coverage of Salesforce features a scratch org was set up to test this against a live sandbox API.
While this means expanding tests is going to be more work, it also means it's battle tested against real data.

[Get a copy of the scratch org definition](https://github.com/WalrusSoup/salesforce-php-dx)

Copy .env.example to .env, update the redirect_url to be your local machine URL. Set the base_url to `https://test.salesforce.com`.

### Scratch Org Setup
1. Signup for a developer edition organization [here](https://developer.salesforce.com/signup)
2. Login and head to the Dev Hub `/lightning/setup/DevHub/home` and turn the slider to `enabled`
3. Install the Salesforce DX CLI
4. Pull the salesforce-php-dx project
5. In terminal, type `sfdx force:auth:web:login --setdefaultdevhubusername` and login to the developer hub
6. Create a scratch org`sfdx force:org:create -f config/project-scratch-def.json --setalias salesforcephpdx --durationdays 7 --setdefaultusername --json --loglevel fatal`
7. Use `sfdx force:org:open` to open your scratch organization
8. Execute the apex in `scripts/apex/seed.apex` in dev console (or use VSCODE)

### Password Flow
Password flow is possible using the `SalesforceApiUserLogin` class. Please do an API only user profile for this, and ensure you use
whitelisted IP addresses on production if you take this approach. You can configure this by setting the method to `AUTH_METHOD` inside of `.env` to `login`.
```php
$apiUserLogin = new SalesforceApiUserLogin();
$apiUserLogin->configureApp('MY_CONSUMER_KEY', 'MY_CONSUMER_SECRET');
$credentials = $apiUserLogin->authenticateUser('USERNAME', 'PASSWORD');
if(!$credentials['result']) {
    // auth failed, check the message key
}
$api = new SalesforceApi($credentials['access_token'], $credentials['instance_url'], '42.0');
$api->getLimits();
```

### Security Token
Please visit `YOUR_DOMAIN.com/_ui/system/security/ResetApiTokenEdit` to get a security token reset. It will email the user. This must be
appended to the back of the password when authenticating with the password flow **unless** you are using a whitelisted IP address range.

### OAuth Flow (Recommended)
When deploying the above, a new application should be installed called `SalesforcePhpApi` under `Apps -> App Manager`, find
SalesforcePhpApi, click the dropdown and select `View`

1. Adjust the callback URL to match your local machine
2. Copy the consumer secret and key into your .env file
3. Launch this project's `index.php` in a browser, it should redirect you to authenticate against the scratch organization

Here is how it can work in your application:

```php
$sfOauthConfiguration = new \myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration();
$sfOauthConfiguration->setClientId('client_id');
$sfOauthConfiguration->setSecret('secret');
$sfOauthConfiguration->setRedirectUri('redirect_uri');
$sfOauthConfiguration->setBaseUrl('base_uri');

$connector = new \myoutdeskllc\SalesforcePhp\Connectors\SalesforceOAuthLoginConnector();
$connector->setOauthConfiguration($sfOauthConfiguration);
$authorizationUrl = $connector->getAuthorizationUrl();
$state = $connector->getState();

// redirect to the authorization url and store the state locally in session
$_GET['state'] = 'state_from_saloon';
$_GET['code'] = 'code_from_salesforce';

$connector = new \myoutdeskllc\SalesforcePhp\Connectors\SalesforceOAuthLoginConnector();
$authenticator = $connector->getAccessToken($code, $state);

// store this in an encrypted field in the database
$serialized = $authenticator->serialize();
// unserialize it later
$authenticator = AccessTokenAuthenticator::unserialize($serialized);

if ($authenticator->hasExpired() === true) {
    $authConnector = new \myoutdeskllc\SalesforcePhp\Connectors\SalesforceOAuthLoginConnector();
    
    // configure your auth connector again, then refresh the access token, passing the authenticator to it
    $authenticator = $authConnector->refreshAccessToken($authenticator);
    
    $user->auth = $authenticator;
    $user->save();
}
```
