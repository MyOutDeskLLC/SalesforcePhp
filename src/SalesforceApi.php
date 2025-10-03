<?php

namespace myoutdeskllc\SalesforcePhp;

use InvalidArgumentException;
use JsonException;
use myoutdeskllc\SalesforcePhp\Api\BulkApi2;
use myoutdeskllc\SalesforcePhp\Api\ReportApi;
use myoutdeskllc\SalesforcePhp\Api\SObjectApi;
use myoutdeskllc\SalesforcePhp\Api\StandardObjectApi;
use myoutdeskllc\SalesforcePhp\Api\ToolingApi;
use myoutdeskllc\SalesforcePhp\Connectors\SalesforceApiConnector;
use myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration;
use myoutdeskllc\SalesforcePhp\Requests\Auth\LoginApiUser;
use myoutdeskllc\SalesforcePhp\Requests\Organization\GetLimits;
use myoutdeskllc\SalesforcePhp\Requests\Organization\GetSupportedApiVersions;
use myoutdeskllc\SalesforcePhp\Requests\Query\ExecuteQuery;
use myoutdeskllc\SalesforcePhp\Requests\Query\Search;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\CreateRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\CreateRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\DeleteRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\DeleteRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\UpdateRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\UpdateRecords;
use myoutdeskllc\SalesforcePhp\QueryBuilder\SoqlQueryBuilder;
use myoutdeskllc\SalesforcePhp\Exceptions\InvalidQueryException;
use Saloon\Contracts\OAuthAuthenticator;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;

class SalesforceApi
{
    protected Connector $connector;
    protected bool $async = false;
    protected bool $eatErrors = false;
    protected bool $recordsOnly = false;
    protected static string $apiVersion = 'v51.0';
    protected static string $instanceUrl = 'https://test.salesforce.com';

    public function __construct(string $instanceUrl = 'https://test.salesforce.com', string $apiVersion = 'v51.0')
    {
        self::$instanceUrl = $instanceUrl;
        self::$apiVersion = $apiVersion;
    }

    public function setApiVersion(string $version): void
    {
        self::$apiVersion = $version;
    }

    public function setInstanceUrl(string $instanceUrl): void
    {
        self::$instanceUrl = $instanceUrl;
    }

    public function login(string $username, string $password, string $consumerKey, string $consumerSecret): string
    {
        $this->connector = new Connectors\SalesforceApiConnector();

        $loginRequest = new LoginApiUser();
        $loginRequest->body()->set([
            'grant_type'    => 'password',
            'client_id'     => $consumerKey,
            'client_secret' => $consumerSecret,
            'username'      => $username,
            'password'      => $password,
        ]);

        $response = $this->connector->send($loginRequest)->json();

        // this must be set after the login request for both OAuth and username / password flows
        self::$instanceUrl = $response['instance_url'];

        $this->connector->withTokenAuth($response['access_token']);

        return $response['access_token'];
    }

    public function restoreAccessToken(string $accessToken): void
    {
        $this->connector->withTokenAuth($accessToken);
    }

    public function startOAuthLogin(OAuthConfiguration $configuration): array
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $connector->setOauthConfiguration($configuration);

        $authorizationUrl = $connector->getAuthorizationUrl();
        // If we have a code challenge, we need to include it here
        if (!empty($configuration->getCodeChallenge())) {
            $base64Challenge = base64_encode(hex2bin($configuration->getCodeChallenge()));
            $base64Challenge = str_replace(['+', '/', '='], ['-', '_', ''], $base64Challenge);
            $authorizationUrl .= '&code_challenge='.$base64Challenge.'&code_challenge_method=S256';
        }

        return [
            'url'   => $authorizationUrl,
            'state' => $connector->getState(),
        ];
    }

    public function completeOAuthLogin(OAuthConfiguration $configuration, string $code, string $state, string $codeVerifier = ''): OAuthAuthenticator
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $connector->setOauthConfiguration($configuration, $codeVerifier);
        $authenticator = $connector->getAccessToken($code, $state);

        $this->connector = new SalesforceApiConnector();
        $this->connector->authenticate($authenticator);

        return $authenticator;
    }

    public function restoreExistingOAuthConnectionWithCodeVerification($serializedAuthenticator, OAuthConfiguration $originalConfiguration, string $codeVerifier, callable $afterRefresh)
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $connector->setOauthConfiguration($originalConfiguration, $codeVerifier);
        $authenticator = AccessTokenAuthenticator::unserialize($serializedAuthenticator);
        $connector->authenticate($authenticator);

        if ($authenticator->hasExpired() || $authenticator->getExpiresAt() === null) {
            $authenticator = $connector->refreshAccessToken($authenticator);
            $afterRefresh($authenticator);
        }

        $this->connector = new SalesforceApiConnector();
        $this->connector->authenticate($authenticator);
    }

    public function restoreExistingOAuthConnection($serializedAuthenticator, callable $afterRefresh)
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $authenticator = AccessTokenAuthenticator::unserialize($serializedAuthenticator);
        $connector->authenticate($authenticator);

        if ($authenticator->hasExpired()) {
            $authenticator = $connector->refreshAccessToken($authenticator);
            $afterRefresh($authenticator);
        }

        $this->connector = new SalesforceApiConnector();
        $this->connector->authenticate($authenticator);
    }

    public function refreshToken($serializedAuthenticator, callable $afterRefresh)
    {
        $this->restoreExistingOAuthConnection($serializedAuthenticator, $afterRefresh);
    }

    public static function getApiVersion(): string
    {
        return self::$apiVersion;
    }

    public static function getInstanceUrl(): string
    {
        return self::$instanceUrl;
    }

    /**
     * Get a specific record from salesforce by object name and ID.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_retrieve.htm
     *
     * @param string $object
     * @param string $id
     * @param array  $fields
     *
     * @return array|null
     */
    public function getRecord(string $object, string $id, array $fields): ?array
    {
        if (empty($object)) {
            throw new InvalidArgumentException('Given object cannot be empty');
        }
        if (empty($id)) {
            throw new InvalidArgumentException('Given ID Cannot be empty');
        }
        if (empty($fields)) {
            throw new InvalidArgumentException('You must select at least one field');
        }

        $request = new GetRecord($object, $id);
        $request->query()->add('fields', implode(',', $fields));

        return $this->executeRequest($request);
    }

    /**
     * Executes the request, extracting records only if needed.
     *
     * @return array|null
     */
    protected function executeRequest(Request $request): ?array
    {
        return $this->unpackResponseIfNeeded($this->executeRequestSync($request));
    }

    /**
     * Executes the request directly, allowing the caller to handle the specifics of the response (it may not be JSON).
     *
     * @param Request $request
     *
     * @return Response
     */
    protected function executeRequestDirectly(Request $request): Response
    {
        return $this->executeRequestSync($request);
    }

    /**
     * Unpacks a response object if needed.
     *
     * @param Response $response
     *
     * @throws JsonException
     *
     * @return array|mixed|mixed[]
     */
    protected function unpackResponseIfNeeded(Response $response): mixed
    {
        if ($response->successful() && empty($response->body())) {
            return [];
        }

        $inlineData = $response->json();

        if (isset($inlineData['records']) && $this->recordsOnly) {
            return array_map(function ($item) {
                unset($item['attributes']);

                return $item;
            }, $inlineData['records']);
        }

        return $response->json();
    }

    /**
     * Configures this API to unset the 'attribute' key and return only records.
     *
     * @return $this
     */
    public function recordsOnly(): self
    {
        $this->recordsOnly = true;

        return $this;
    }

    /**
     * Executes a request synchronously.
     */
    protected function executeRequestSync(Request $request): Response
    {
        if ($this->eatErrors) {
            return $this->connector->send($request);  // @phpstan-ignore-line
        }

        $response = $this->connector->send($request);

        if ($response->failed()) {
            $response->throw();
        }

        return $response; // @phpstan-ignore-line
    }

    /**
     * Creates a record in salesforce. This may fail depending on what is in the payload (such as a missing piece of information).
     *
     * @param string $object
     * @param array  $recordInformation
     *
     * @return array|null
     */
    public function createRecord(string $object, array $recordInformation): ?array
    {
        $request = new CreateRecord($object);
        $request->body()->set($recordInformation);

        return $this->executeRequest($request);
    }

    /**
     * Updates the salesforce object with the given type and ID with new information.
     *
     * @param string $object
     * @param string $id
     * @param array  $recordInformation
     *
     * @return array
     */
    public function updateRecord(string $object, string $id, array $recordInformation): array
    {
        $request = new UpdateRecord($object, $id);
        $request->body()->set($recordInformation);

        return $this->executeRequest($request);
    }

    /**
     * Updates the given records with the composite API.
     *
     * @param string $object
     * @param array  $recordInformation
     * @param bool   $allOrNone
     *
     * @return array
     */
    public function updateRecords(string $object, array $recordInformation, bool $allOrNone = true): array
    {
        $payload = [
            'allOrNone' => $allOrNone,
            'records'   => array_map(function ($field) use ($object) {
                // Set the attributes key to contain the type of object for the composite API to use
                $field['attributes'] = [
                    'type' => $object,
                ];

                return $field;
            }, $recordInformation),
        ];

        $request = new UpdateRecords();
        $request->body()->set($payload);

        return $this->executeRequest($request);
    }

    /**
     * Deletes a record from salesforce.
     *
     * @param string $object
     * @param string $id
     *
     * @return bool
     */
    public function deleteRecord(string $object, string $id): bool
    {
        $request = new DeleteRecord($object, $id);

        return $this->executeRequestDirectly($request)->status() === 204;
    }

    /**
     * Deletes records from salesforce using the composite API.
     *
     * @param array $ids
     * @param bool  $allOrNone
     *
     * @return array
     */
    public function deleteRecords(array $ids, bool $allOrNone = true): array
    {
        $request = new DeleteRecords();
        $request->query()->set([
            'ids'       => implode(',', $ids),
            'allOrNone' => $allOrNone,
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Creates records in salesforce using the composite API.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_composite_sobjects_collections_create.htm
     *
     * @param string $object          object to create. Include __c for custom objects.
     * @param array  $recordsToInsert array of records to insert
     * @param bool   $allOrNone       should this operation fail if any are not inserted
     *
     * @return array
     */
    public function createRecords(string $object, array $recordsToInsert, bool $allOrNone = true): array
    {
        $payload = [
            'allOrNone' => $allOrNone,
            'records'   => array_map(function ($field) use ($object) {
                // Set the attributes key to contain the type of object for the composite API to use
                $field['attributes'] = [
                    'type' => $object,
                ];

                return $field;
            }, $recordsToInsert),
        ];

        $request = new CreateRecords();
        $request->body()->set($payload);

        return $this->executeRequest($request);
    }

    /**
     * Get records (multiple) from salesforce using a list of ID's.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_composite_sobjects_collections_retrieve.htm
     *
     * @param string $object
     * @param array  $ids
     * @param array  $fields
     *
     * @return array|mixed
     */
    public function getRecords(string $object, array $ids, array $fields)
    {
        if (empty($object)) {
            throw new InvalidArgumentException('You must have select a valid object (custom sObject or standard object)');
        }
        if (empty($ids)) {
            throw new InvalidArgumentException('You must have at least one ID to select');
        }
        if (empty($fields)) {
            throw new InvalidArgumentException('You must select at least one field');
        }
        $request = new GetRecords($object);
        $request->query()->add('ids', implode(',', $ids));
        $request->query()->add('fields', implode(',', $fields));

        return $this->executeRequest($request);
    }

    /**
     * Lists the limits for this organization.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm
     */
    public function getLimits(): ?array
    {
        $request = new GetLimits();

        return $this->executeRequest($request);
    }

    /**
     * Configures this API to return all data (attribute and records).
     *
     * @return $this
     */
    public function allAttributes(): self
    {
        $this->recordsOnly = false;

        return $this;
    }

    /**
     * Returns available API versions for this organizations instance.
     *
     * @return array
     */
    public function listApiVersionsAvailable(): array
    {
        $request = new GetSupportedApiVersions();

        return $this->executeRequest($request);
    }

    /**
     * Search for records across all records.
     *
     * @param string $query query to search for
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_search.htm
     */
    public function search(string $query): array
    {
        $searchRequest = new Search();
        $searchRequest->query()->set(['q' => $query]);

        return $this->executeRequest($searchRequest);
    }

    /**
     * Search for records within a specific object type.
     *
     * @param string $query  query to search for
     * @param string $object object to search for records within
     * @param array  $fields which fields to return from this search (default id, name)
     *
     * @return array
     */
    public function searchIn(string $query, string $object, array $fields = ['Name']): array
    {
        // Drop id as it's included by default
        $fields = array_filter($fields, function ($fieldName) {
            return strtolower($fieldName) !== 'id';
        });

        $searchRequest = new Search();
        $searchRequest->query()->set([
            'q'       => $query,
            'sobject' => $object,
            'fields'  => implode(',', array_map(function ($field) use ($object) {
                return "{$object}.{$field}";
            }, $fields)),
        ]);

        return $this->executeRequest($searchRequest);
    }

    /**
     * helper method to assist in searching for records.
     *
     * @param string $object         sObject name to search in
     * @param array  $properties     array of key value pairs, where the key is the field name
     * @param array  $fieldsToSelect which fields to return from the query
     *
     * @return array
     */
    public function searchObjectWhereProperties(string $object, array $properties, array $fieldsToSelect = ['id']): array
    {
        $builder = self::getQueryBuilder();
        $builder = $builder->select($fieldsToSelect)
            ->from($object);

        foreach ($properties as $fieldName => $fieldValue) {
            $builder->where($fieldName, '=', $fieldValue);
        }

        return $this->executeQuery($builder);
    }

    /**
     * Returns an instance of the SoqlQueryBuilder.
     *
     * @return SoqlQueryBuilder
     */
    public static function getQueryBuilder(): SoqlQueryBuilder
    {
        return new SoqlQueryBuilder();
    }

    /**
     * Executes a query against salesforce. Make sure this is safe on your application end.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm
     *
     * @param SoqlQueryBuilder $builder
     */
    public function executeQuery(SoqlQueryBuilder $builder): array
    {
        return $this->executeQueryRaw($builder->toSoql());
    }

    /**
     * Directly execute SOQL and get results.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm
     *
     * @param string $rawQuery
     *
     * @return array
     */
    public function executeQueryRaw(string $rawQuery): array
    {
        $request = new ExecuteQuery($rawQuery);

        return $this->executeRequest($request);
    }

    /**
     * Returns only one record found for the given sObject, based on its properties.
     *
     * @param string $object         sObject name to search in
     * @param array  $properties     array of key value pairs, where the key is the field name
     * @param array  $fieldsToSelect which fields to return from the query
     *
     * @throws InvalidQueryException
     *
     * @return array|null
     */
    public function findRecord(string $object, array $properties, array $fieldsToSelect): ?array
    {
        $builder = self::getQueryBuilder();
        $builder = $builder->select($fieldsToSelect)
            ->from($object);

        foreach ($properties as $fieldName => $fieldValue) {
            $builder->where($fieldName, '=', $fieldValue);
        }

        $builder->limit(1);
        $results = $this->executeQuery($builder);

        return array_pop($results);
    }

    /**
     * Returns records found for the given sObject, based on its properties (up to 2,000).
     *
     * @param string $object         sObject name to search in
     * @param array  $properties     array of key value pairs, where the key is the field name
     * @param array  $fieldsToSelect which fields to return from the query
     * @param int    $limit          limit the number of records returned (default is max, which is 2,000)
     *
     * @return array|null
     */
    public function findRecords(string $object, array $properties, array $fieldsToSelect, int $limit = 2000): ?array
    {
        $builder = self::getQueryBuilder();
        $builder = $builder->select($fieldsToSelect)
            ->from($object);

        foreach ($properties as $fieldName => $fieldValue) {
            $builder->where($fieldName, '=', $fieldValue);
        }

        $builder->limit($limit);

        return $this->executeQuery($builder);
    }

    /**
     * @return Connector
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * @param Connector $connector
     */
    public function setConnector(Connector $connector): void
    {
        $this->connector = $connector;
    }

    /**
     * Set this to TRUE if you don't want to throw exceptions on errors.
     *
     * @param bool $eatErrors
     *
     * @return SalesforceApi
     */
    public function eatErrors(bool $eatErrors): SalesforceApi
    {
        $this->eatErrors = $eatErrors;

        return $this;
    }

    /**
     * Returns an instance of the ReportApi.
     *
     * @return ReportApi
     */
    public function getReportApi(): ReportApi
    {
        $api = new ReportApi(self::$instanceUrl, self::$apiVersion);
        if ($this->recordsOnly) {
            $api->recordsOnly();
        }
        $api->setConnector($this->getConnector());

        return $api;
    }

    /**
     * Returns an instance of the SObjectApi.
     *
     * @return SObjectApi
     */
    public function getSObjectApi(): SObjectApi
    {
        $api = new SObjectApi(self::$instanceUrl, self::$apiVersion);
        if ($this->recordsOnly) {
            $api->recordsOnly();
        }
        $api->setConnector($this->getConnector());

        return $api;
    }

    /**
     * Returns an instance of the BulkApi2.
     *
     * @return BulkApi2
     */
    public function getBulkApi(): BulkApi2
    {
        $api = new BulkApi2(self::$instanceUrl, self::$apiVersion);
        if ($this->recordsOnly) {
            $api->recordsOnly();
        }
        $api->setConnector($this->getConnector());

        return $api;
    }

    /**
     * Returns an instance of the Standard Object API (mostly helper methods).
     *
     * @return StandardObjectApi
     */
    public function getStandardObjectApi(): StandardObjectApi
    {
        $api = new StandardObjectApi(self::$instanceUrl, self::$apiVersion);
        if ($this->recordsOnly) {
            $api->recordsOnly();
        }
        $api->setConnector($this->getConnector());

        return $api;
    }

    /**
     * Returns an instance of the tooling API.
     *
     * @return ToolingApi
     */
    public function getToolingApi(): ToolingApi
    {
        $api = new ToolingApi(self::$instanceUrl, self::$apiVersion);
        if ($this->recordsOnly) {
            $api->recordsOnly();
        }
        $api->setConnector($this->getConnector());

        return $api;
    }

    public function getCurrentUserInfo(): array
    {
        $request = new Requests\OAuth2\UserInfo();

        return $this->executeRequest($request);
    }
}
