# Salesforce PHP API
A beautiful, extendable API powered by [Saloon](https://github.com/sammyjo20/saloon)

![img](https://github.styleci.io/repos/467300822/shield)

## Introduction
The goal of this API was to create a feature rich, elegant baseline for working with the Salesforce API. Developers can 
install and leverage this API to help them integrate Salesforce easily and beautifully. These endpoints return salesforce
responses, and most of the endpoints contain links to specific documentation.

## Authors
- [JL](https://github.com/WalrusSoup)

## Features
There are many out of the box features ready for you to build upon. Please consult the [tests](/tests/) folder for specific scenarios.

- Login Support
  - OAuth via [league/oauth2](https://oauth2.thephpleague.com/)
  - Username \ Password Flow (API Users)
- Basic Operations
  - Query record(s)
  - Insert record(s)
  - Supports decorated methods for popular common objects
    - Leads
    - Accounts
    - Opportunities
    - Contacts
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
  - Query Basic Information, Metadata
    - List Reports
    - List Dashboard
  - Manage Folders
    - List, Create, Update, Delete report and dashboard folders
  - Locate Folders
    - Find by name, Id
  - Manage Reports
    - Create, Manage, Update, Delete, Copy Reports
  - Manage Dashboards
    - Create, Manage, Update, Delete, Copy Dashboards
  - Results
    - get results of dashboards, components
    - run reports async
- Organization
  - Supported API versions
  - Limits

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

Copy example.creds.json to creds.json, update the redirect_uri to be your local machine URL. Set the base_uri to `https://test.salesforce.com`.

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
whitelisted IP addresses on production if you take this approach. You can configure this by setting the method to `login` inside of `creds.json`.
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
2. Copy the consumer secret and key into your credentials (creds.json) file
3. Launch this project's `index.php` in a browser, it should redirect you to authenticate against the scratch organization

#### OAuth Usage
OAuth is implemented using [league/oauth2](https://oauth2.thephpleague.com/). The index.php has a sample of how to get OAuth
working. The default `method` in `creds.json` is oauth. 

Here is how it can work in your application:
```php
$sfAuth = new SalesforceOAuthConfiguration();
$sfAuth->setClientId('client_id');
$sfAuth->setSecret('secret');
$sfAuth->setRedirectUri('redirect_uri');
$sfAuth->setBaseUrl('base_uri');

$sfAuthenticator = new SalesforceAuthenticator();
$sfAuthenticator->configure($sfAuth);

// Do a redirect to the OAuth URL
header('Location: ' . $sfAuthenticator->getAuthorizationUrl());

// When you approve the app and come back, you can get information here as you normally would
$token = $sfAuthenticator->handleAuthorizationCallback($_GET['code']);
$owner = $sfAuthenticator->getProvider()->getResourceOwner($token);
```

You will likely want to store the refresh token to allow requests at any time, depending on your applications needs.
