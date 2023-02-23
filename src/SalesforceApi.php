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
use myoutdeskllc\SalesforcePhp\Connectors\SalesforceOAuthLoginConnector;
use myoutdeskllc\SalesforcePhp\OAuth\OAuthConfiguration;
use myoutdeskllc\SalesforcePhp\Requests\Auth\LoginApiUser;
use myoutdeskllc\SalesforcePhp\Requests\Organization\GetLimits;
use myoutdeskllc\SalesforcePhp\Requests\Organization\GetSupportedApiVersions;
use myoutdeskllc\SalesforcePhp\Requests\Query\ExecuteQuery;
use myoutdeskllc\SalesforcePhp\Requests\Query\Search;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\CreateRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\CreateRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\UpdateRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\UpdateRecords;
use myoutdeskllc\SalesforcePhp\Support\SoqlQueryBuilder;
use SalesforceQueryBuilder\Exceptions\InvalidQueryException;
use SalesforceQueryBuilder\QueryBuilder;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;

class SalesforceApi
{
    protected static Connector $connector;
    protected bool $async = false;
    protected bool $recordsOnly = false;

    protected static $apiVersion = 'v51.0';

    protected static $instanceUrl;

    public function __construct()
    {
    }

    public function setApiVersion(string $version): void
    {
        self::$apiVersion = $version;
    }

    public function login(string $username, string $password, string $consumerKey, string $consumerSecret, bool $sandbox = true): void
    {
        $connector = new Connectors\SalesforceApiConnector();
        $loginRequest = new LoginApiUser();
        $loginRequest->body()->set([
            'grant_type'    => 'password',
            'client_id'     => $consumerKey,
            'client_secret' => $consumerSecret,
            'username'      => $username,
            'password'      => $password,
        ]);
        if ($sandbox) {
            $connector->useSandbox();
        } else {
            $connector->useProduction();
        }

        $response = $connector->send($loginRequest);

        self::$connector->withTokenAuth($response['access_token']);
    }

    public function startOAuthLogin(OAuthConfiguration $configuration): array
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $connector->setOauthConfiguration($configuration);

        return [
            'state' => $connector->getState(),
            'url'   => $connector->getAuthorizationUrl(),
        ];
    }

    public function validateOAuthLogin(string $code, string $state)
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $authenticator = $connector->getAccessToken($code, $state);

        self::$connector = new SalesforceApiConnector();
        self::$connector->authenticate($authenticator);

        return $authenticator;
    }

    public function useOAuthLogin($serializedConnection, callable $afterRefresh)
    {
        $connector = new Connectors\SalesforceOAuthLoginConnector();
        $authenticator = SalesforceOAuthLoginConnector::unseriaize($serializedConnection);

        if ($authenticator->hasExpired()) {
            $authenticator = $connector->refreshAccessToken($authenticator);
            $afterRefresh($authenticator);
        }

        self::$connector = new SalesforceApiConnector();
        self::$connector->authenticate($authenticator);
    }

    public static function getApiVersion(): string
    {
        return self::$apiVersion;
    }

    /**
     * Returns an instance of the ReportApi.
     *
     * @return ReportApi
     */
    public static function getReportApi(): ReportApi
    {
        return new ReportApi(self::$connector);
    }

    /**
     * Returns an instance of the SObjectApi.
     *
     * @return SObjectApi
     */
    public static function getSObjectApi(): SObjectApi
    {
        return new SObjectApi(self::$connector);
    }

    /**
     * Returns an instance of the BulkApi2.
     *
     * @return BulkApi2
     */
    public static function getBulkApi(): BulkApi2
    {
        return new BulkApi2(self::$connector);
    }

    /**
     * Returns an instance of the Standard Object API (mostly helper methods).
     *
     * @return StandardObjectApi
     */
    public static function getStandardObjectApi(): StandardObjectApi
    {
        return new StandardObjectApi(self::$connector);
    }

    /**
     * Returns an instance of the tooling API.
     *
     * @return ToolingApi
     */
    public static function getToolingApi(): ToolingApi
    {
        return new ToolingApi(self::$connector);
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
     * @return array|mixed
     */
    public function getRecord(string $object, string $id, array $fields)
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
     * Unpacks a response object if needed.
     *
     * @param Response $response
     *
     * @throws JsonException
     *
     * @return array|mixed|mixed[]
     */
    protected function unpackResponseIfNeeded(Response $response)
    {
        if ($this->recordsOnly()) {  // @phpstan-ignore-line
            return array_map(function ($item) {
                unset($item['attributes']);

                return $item;
            }, $response->json('records'));
        }

        return $response->json();  // @phpstan-ignore-line
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
        $request->authenticate($this->authenticator);

        return self::$connector->send($request);  // @phpstan-ignore-line
    }

    /**
     * Creates a record in salesforce. This may fail depending on what is in the payload (such as a missing piece of information).
     *
     * @param string $object
     * @param array  $recordInformation
     *
     * @return array|mixed
     */
    public function createRecord(string $object, array $recordInformation)
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
     * @param QueryBuilder $builder
     */
    public function executeQuery(QueryBuilder $builder)
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

    protected function executeRequestDirectly(Request $request): Response
    {
        return $this->executeRequestSync($request);
    }
}
