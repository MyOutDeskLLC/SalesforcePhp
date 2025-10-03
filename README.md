# Salesforce PHP API
A beautiful, extendable API powered by [Saloon](https://github.com/sammyjo20/saloon).

![img](https://github.styleci.io/repos/467300822/shield)
![MyOutDeskLLC](https://circleci.com/gh/MyOutDeskLLC/SalesforcePhp.svg?style=shield)

## Introduction
This API provides a feature rich, elegant baseline for working with the Salesforce API. Developers can 
install and leverage this API to help them integrate Salesforce easily and beautifully. These endpoints return salesforce
responses, and most of the endpoints contain links to specific documentation.

## Installation
`composer require myoutdeskllc/salesforce-php` 

## Features
There are many out of the box features ready for you to build upon.

- Authentication
  - OAuth
  - OAuth + PKCE
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
  - Access SOQL Query Builder
  - Helper functions for SOQL Date Functions
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

### Authentication
Two authentication methods are supported. OAuth and Username \ Password. OAuth is the preferred method unless you are using server to server integrations.

#### OAuth
OAuth is the preferred method of authentication. First, you will need to create an application in your Salesforce instance, then 
copy the redirect_uri, client id, and client secret to get started.

```php
use \myoutdeskllc\SalesforcePhp\SalesforceApi;
use \myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration;

$salesforceApi = new SalesforceApi('https://MY_INSTANCE.my.salesforce.com');

$sfOauthConfiguration = OAuthConfiguration::create(
    'client_id' => 'YOUR_CLIENT_ID',
    'secret' => 'YOUR_SECRET',
    'redirect_uri' => 'YOUR_REDIRECT_URI'
);

[$url, $state] = array_values($salesforceApi->startOAuthLogin($oauthConfig));
// store the state yourself in the users session and redirect to $url
// once the user is redirected back to your application, you can get the access token
$authenticator = $salesforceApi->completeOAuthLogin($oauthConfig, $code, $state);
// store this in an encrypted field in your database
$serialized = $authenticator->serialize();
```

#### OAuth + PKCE
Pass in a `code_verifier` parameter into the oAuth configuration and it will kick off the flow for this. You'll need to store the
`code_verifier` parameter on your own end - make it random of course. Library will handle the oddities of SHA256 + base64url encoding for you.

```php
$oauthConfig = OAuthConfiguration::create([
    'client_id'     => 'SALESFORCE_CONSUMER_KEY',
    'client_secret' => 'SALESFORCE_CONSUMER_SECRET',
    'redirect_uri'  => 'REDIRECT_URI',
    'code_verifier' => 'code-verifier-challenge-make-sure-this-is-random-and-not-shown-to-user',
]);
```

It's a little hacky, but this is not implemented upstream and I haven't updated Saloon so it is what it is.

#### Password Authentication
Please visit `YOUR_DOMAIN.com/_ui/system/security/ResetApiTokenEdit` to get a security token reset. It will email the user. This must be
appended to the back of the password when authenticating with the password flow **unless** you are using a whitelisted IP address range.

```php
$salesforceApi = new \myoutdeskllc\SalesforcePhp\SalesforceApi('https://MY_INSTANCE.my.salesforce.com');
// this call will return an access_token for you to cache in your own database for a time
$salesforceApi->login('username', 'password', 'consumer_key', 'consumer_secret');
// if you have an access token that is still valid, you can restore it
// I recommend caching this for at least 5 minutes, so you don't bombard salesforce with password requests
// if you new up a new instance of the API, you can restore the access token from previous authentications
$salesforceApi->restoreAccessToken('access_token');
```

## Record Level Operations
There are several methods available to execute CRUD operations on records in salesforce.

### Create Records
You may either use CreateRecord or CreateRecords to insert records into Salesforce.

```php
$salesforceApi->createRecord('My_Custom_Object__c', [
    'Field__c' => 'Value',
    'Other_Field__c' => 'Other Value'
]);
// or
$salesforceApi->createRecords('My_Custom_Object__c', [
    [
        'Field__c' => 'Value',
        'Other_Field__c' => 'Other Value'
    ],
    [
        'Field__c' => 'Value',
        'Other_Field__c' => 'Other Value'
    ]
]);
```

When creating multiple records, you may pass in a third boolean for allOrNone to determine if they should all succeed for the operation to be committed in salesforce.

### Read Records
The API provides a few different ways to query records. The most common way is to use the `getRecord` method.

```php
$salesforceApi->getRecord('My_Custom_Object__c', 'ACCOUNT_ID', ['Field__c', 'Other_Field__c']);
```

You may also `queryRecords` to get several records at once.

```php
$salesforceApi->getRecords('My_Custom_Object__c', ['id1','id2','id3'], ['Field__c', 'Other_Field__c']);
```

### Update Records
You may update records using the `updateRecord` method.

```php
$salesforceApi->updateRecord('My_Custom_Object__c', 'ACCOUNT_ID', [
    'Field__c' => 'Value',
    'Other_Field__c' => 'Other Value'
]);
// or
$salesforceApi->updateRecords('My_Custom_Object__c', [
    [
        'Id' => 'ACCOUNT_ID',
        'Field__c' => 'Value',
        'Other_Field__c' => 'Other Value'
    ],
    [
        'Id' => 'ACCOUNT_ID',
        'Field__c' => 'Value',
        'Other_Field__c' => 'Other Value'
    ]
]);
```

### Delete Records
You may delete records using the `deleteRecord` method.

```php
$salesforceApi->deleteRecord('My_Custom_Object__c', 'ACCOUNT_ID');
// or using composite api
$salesforceApi->deleteRecords(['id1','id2','id3']);
```
allOrNone is supported for delete operations as a second parameter.

When updating multiple records, you may pass in a third boolean for allOrNone to determine if they should all succeed for the operation to be committed in salesforce.

### Searching For Records
The API provides a few different ways to search for records.

#### Searching within specific Objects
```php
$salesforceApi->searchIn('Account Name', 'Account');
// iterate over the returned results
foreach($results['searchRecords'] as $record) {
    // do something with the record
}
```

#### Searching within specific objects, within a specific field
```php
$salesforceApi->searchIn('Company, LLC', 'Account', ['Name', 'Company']);
// iterate over the returned results
foreach($results['searchRecords'] as $record) {
    // do something with the record
}
```

#### Searching across all objects:
```php
$api->search('Hint inside records somewhere');
foreach($results['searchRecords'] as $record) {
    // do something with the record
}
```

### Salesforce Native Objects
The API provides a few helper methods to get common objects.

```php
$standardObjectApi = $salesforceApi->getStandardObjectApi();
$standardObjectApi->createLead($myLeadData);
$standardObjectApi->getLead($leadId, ['Id', 'Name']);
$standardObjectApi->getLeads(['id1','id2','id3'], ['Id', 'Name']);
```

You can see a full list of operations available in the [StandardObjectApi](/src/api/StandardObjectApi.php)

## Batch Jobs
Batch job support for records is available via a job wrapper, completed with CSV support.

```php
// make sure you have a new api first to pass in
$salesforceJob = new SalesforceJob($api->getBulkApi());
$salesforceJob->setObject('My_Object__c');
$salesforceJob->setOperation(BulkApiOptions::INSERT);
$salesforceJob->initJob();
$salesforeJob->getJobId();
// prints a job id
$salesforceJob->setCsvFile('path/to/file.csv');
// this will set a CSV file stream for you, otherwise, set up a file yourself with fopen
$salesforceJob->setFileStream(fopen('path/to/file.csv', 'r'));
// or, just set up records directly as an array
$salesforceJob->setRecords([
    [
        'Field__c' => 'Value',
        'Other_Field__c' => 'Other Value'
    ],
    [
        'Field__c' => 'Value',
        'Other_Field__c' => 'Other Value'
    ]
]);
// don't forget to "close" the job to lock it for salesforce so it begins processing
$salesforceJob->closeJob();
// then later, you can check the status
$salesforceJob = SalesforceJob::getExistingJobById($jobId, $api->getBulkApi());
$salesforceJob->refreshStatus();
// check the state to see if its done
$salesforceJob->getState();
// returns 'JobComplete' if its finished
// then, of course, you need to know what actually was returned
// Row 1 is headers, so you'll want to skip that
$salesforceJob->getSuccessfulResultsAsArray();
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

## Contributors
- [JL](https://github.com/WalrusSoup)
