<?php

namespace myoutdeskllc\SalesforcePhp;

use \InvalidArgumentException;
use Sammyjo20\Saloon\Http\SaloonRequest;
use myoutdeskllc\SalesforcePhp\Api\BulkApi2;
use myoutdeskllc\SalesforcePhp\Api\ReportApi;
use myoutdeskllc\SalesforcePhp\Api\SObjectApi;
use myoutdeskllc\SalesforcePhp\Traits\HasApiTokens;
use myoutdeskllc\SalesforcePhp\Api\StandardObjectApi;
use myoutdeskllc\SalesforcePhp\Helpers\SoqlQueryBuilder;
use myoutdeskllc\SalesforcePhp\Requests\Organization\GetLimits;
use myoutdeskllc\SalesforcePhp\Requests\Organization\GetSupportedApiVersions;
use myoutdeskllc\SalesforcePhp\Requests\Query\ExecuteQuery;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\CreateRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\CreateRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\UpdateRecord;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\UpdateRecords;

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
     * Get a specific record from salesforce by object name and ID
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_retrieve.htm
     *
     * @param string $object
     * @param string $id
     * @param array $fields
     * @return array|mixed
     */
    public function getRecord(string $object, string $id, array $fields)
    {
        if(empty($object)) {
            throw new InvalidArgumentException('Given object cannot be empty');
        }
        if(empty($id)) {
            throw new InvalidArgumentException('Given ID Cannot be empty');
        }
        if(empty($fields)) {
            throw new InvalidArgumentException('You must select at least one field');
        }

        $request = new GetRecord($object, $id);
        $request->addQuery('fields', implode(',', $fields));

        return $this->executeRequest($request);
    }

    /**
     * Creates a record in salesforce. This may fail depending on what is in the payload (such as a missing piece of information)
     *
     * @param string $object
     * @param array $recordInformation
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
     * Updates the salesforce object with the given type and ID with new information
     *
     * @param string $object
     * @param string $id
     * @param array $recordInformation
     * @return array|mixed
     */
    public function updateRecord(string $object, string $id, array $recordInformation)
    {
        $request = new UpdateRecord($object, $id);
        $request->setData($recordInformation);

        return $this->executeRequest($request);
    }

    /**
     * Updates the given records with the composite API
     *
     * @param string $object
     * @param array $recordInformation
     * @param bool $allOrNone
     * @return array|mixed
     */
    public function updateRecords(string $object, array $recordInformation, bool $allOrNone = true)
    {
        $payload = [
            'allOrNone' => $allOrNone,
            'records' => array_map(function($field) use ($object) {
                // Set the attributes key to contain the type of object for the composite API to use
                $field['attributes'] = [
                    'type' => $object
                ];

                return $field;
            }, $recordInformation)
        ];

        $request = new UpdateRecords();
        $request->setData($payload);

        return $this->executeRequest($request);
    }

    /**
     * Creates records in salesforce using the composite API
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_composite_sobjects_collections_create.htm
     *
     * @param string $object
     * @param array $recordsToInsert array of records to insert
     * @param bool $allOrNone should this operation fail if any are not inserted
     */
    public function createRecords(string $object, array $recordsToInsert, bool $allOrNone = true)
    {
        $payload = [
            'allOrNone' => $allOrNone,
            'records' => array_map(function($field) use ($object) {
                // Set the attributes key to contain the type of object for the composite API to use
                $field['attributes'] = [
                    'type' => $object
                ];

                return $field;
            }, $recordsToInsert)
        ];

        $request = new CreateRecords();
        $request->setData($payload);

        return $this->executeRequest($request);
    }

    /**
     * Get records (multiple) from salesforce using a list of ID's
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_composite_sobjects_collections_retrieve.htm
     *
     * @param string $object
     * @param array $ids
     * @param array $fields
     * @return array|mixed
     */
    public function getRecords(string $object, array $ids, array $fields)
    {
        if(empty($object)) {
            throw new InvalidArgumentException('You must have select a valid object (custom sObject or standard object)');
        }
        if(empty($ids)) {
            throw new InvalidArgumentException('You must have at least one ID to select');
        }
        if(empty($fields)) {
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
     * @return array|mixed
     * @throws \SalesforceQueryBuilder\Exceptions\InvalidQueryException
     */
    public function executeQuery(SoqlQueryBuilder $builder)
    {
        return $this->executeQueryRaw($builder->toSoql());
    }

    /**
     * Directly execute SOQL and get results
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm
     *
     * @param string $rawQuery
     * @return array|mixed
     */
    public function executeQueryRaw(string $rawQuery)
    {
        $request = new ExecuteQuery($rawQuery);

        return $this->executeRequest($request);
    }

    /**
     * Lists the limits for this organization
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm
     */
    public function getLimits()
    {
        $request = new GetLimits();

        return $this->executeRequest($request);
    }

    protected function executeRequest(SaloonRequest $request)
    {
        $response = $request->send()->json();
        if($this->recordsOnly() && isset($response['records'])) {
            return array_map(function($item) {
                unset($item['attributes']);
                return $item;
            }, $response['records']);
        }

        return $response;
    }

    public function recordsOnly() : self
    {
        $this->recordsOnly = true;

        return $this;
    }

    public function allAttributes() : self
    {
        $this->recordsOnly = false;

        return $this;
    }

    public function listApiVersionsAvailable()
    {
        $request = new GetSupportedApiVersions();

        return $request->send()->json();
    }

    public static function getReportApi()
    {
        return new ReportApi(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    public static function getSObjectApi()
    {
        return new SObjectApi(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    public static function getBulkApi()
    {
        return new BulkApi2(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    public static function getStandardObjectApi() : StandardObjectApi
    {
        return new StandardObjectApi(self::$token, self::$instanceUrl, self::$apiVersion);
    }

    public static function getQueryBuilder() : SoqlQueryBuilder
    {
        return new SoqlQueryBuilder();
    }
}