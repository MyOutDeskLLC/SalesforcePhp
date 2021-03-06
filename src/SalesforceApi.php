<?php

namespace myoutdeskllc\SalesforcePhp;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use myoutdeskllc\SalesforcePhp\Api\BulkApi2;
use myoutdeskllc\SalesforcePhp\Api\ReportApi;
use myoutdeskllc\SalesforcePhp\Api\SObjectApi;
use myoutdeskllc\SalesforcePhp\Api\StandardObjectApi;
use myoutdeskllc\SalesforcePhp\Api\ToolingApi;
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
use myoutdeskllc\SalesforcePhp\Traits\HasApiTokens;
use Sammyjo20\Saloon\Exceptions\SaloonException;
use Sammyjo20\Saloon\Http\SaloonRequest;

class SalesforceApi
{
    use HasApiTokens;

    protected bool $recordsOnly = false;

    public function __construct(string $accessToken, string $instanceUrl, string $apiVersion = null)
    {
        self::$token = $accessToken;
        self::$instanceUrl = $instanceUrl;
        self::$apiVersion = $apiVersion ?? null;
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
        $request->addQuery('fields', implode(',', $fields));

        return $this->executeRequest($request);
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
        $request->setData($recordInformation);

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
        $request->setData($recordInformation);

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
        $request->setData($payload);

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
        $request->setData($payload);

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
        $request->addQuery('ids', implode(',', $ids));
        $request->addQuery('fields', implode(',', $fields));

        return $this->executeRequest($request);
    }

    /**
     * Executes a query against salesforce. Make sure this is safe on your application end.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm
     *
     * @param SoqlQueryBuilder $builder
     *
     * @throws \SalesforceQueryBuilder\Exceptions\InvalidQueryException
     *
     * @return array|mixed
     */
    public function executeQuery(SoqlQueryBuilder $builder)
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
     * Lists the limits for this organization.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm
     */
    public function getLimits()
    {
        $request = new GetLimits();

        return $this->executeRequest($request);
    }

    /**
     * Executes the request, extracting records only if needed.
     *
     * @param SaloonRequest $request
     *
     * @throws GuzzleException|SaloonException|\ReflectionException
     *
     * @return array|null
     */
    protected function executeRequest(SaloonRequest $request): ?array
    {
        $response = $request->send()->json();

        if (isset($response['records']) && $this->recordsOnly()) {
            return array_map(function ($item) {
                unset($item['attributes']);

                return $item;
            }, $response['records']);
        }

        return $response;
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
        $searchRequest = (new Search())->setQuery(['q' => $query]);

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

        $searchRequest = (new Search())->setQuery([
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
     * Returns only one record found for the given sObject, based on its properties.
     *
     * @param string $object         sObject name to search in
     * @param array  $properties     array of key value pairs, where the key is the field name
     * @param array  $fieldsToSelect which fields to return from the query
     *
     * @throws \SalesforceQueryBuilder\Exceptions\InvalidQueryException
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
     * Returns an instance of the ReportApi.
     *
     * @return ReportApi
     */
    public static function getReportApi(): ReportApi
    {
        return new ReportApi(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    /**
     * Returns an instance of the SObjectApi.
     *
     * @return SObjectApi
     */
    public static function getSObjectApi(): SObjectApi
    {
        return new SObjectApi(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    /**
     * Returns an instance of the BulkApi2.
     *
     * @return BulkApi2
     */
    public static function getBulkApi(): BulkApi2
    {
        return new BulkApi2(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    /**
     * Returns an instance of the Standard Object API (mostly helper methods).
     *
     * @return StandardObjectApi
     */
    public static function getStandardObjectApi(): StandardObjectApi
    {
        return new StandardObjectApi(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    /**
     * Returns an instance of the tooling API.
     *
     * @return ToolingApi
     */
    public static function getToolingApi(): ToolingApi
    {
        return new ToolingApi(self::$token, self::$instanceUrl, self::$apiVersion);
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
}
