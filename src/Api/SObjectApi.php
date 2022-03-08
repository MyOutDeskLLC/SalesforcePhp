<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use myoutdeskllc\SalesforcePhp\Requests\SObjects\DescribeGlobal;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\DescribeObject;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetDeletedRecords;
use myoutdeskllc\SalesforcePhp\Requests\SObjects\GetObjectInformation;
use myoutdeskllc\SalesforcePhp\SalesforceApi;

class SObjectApi extends SalesforceApi
{
    /**
     * Return basic information about the Object.
     *
     * @param string $object object to query. Include __c if this is a custom object.
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_basic_info.htm
     */
    public function getObjectInformation(string $object): array
    {
        $request = new GetObjectInformation($object);

        return $this->executeRequest($request);
    }

    /**
     * Return basic information about all objects available to this instance. This list will be very large as it includes standard objects.
     *
     * @return array
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_describeGlobal.htm
     */
    public function listObjects(): array
    {
        $request = new DescribeGlobal();

        return $this->executeRequest($request);
    }

    /**
     * Return full metadata information about the object.
     *
     * @param string $object object to query. Include __c for custom objects.
     *
     * @return array|mixed
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_describe.htm
     */
    public function describeObject(string $object)
    {
        $request = new DescribeObject($object);

        return $this->executeRequest($request);
    }

    /**
     * Returns object fields, but with limited information that is more easily understandable for serialization, etc.
     *
     * @param string $object            object to query. include __c for custom objects
     * @param array  $additionalSelects additional fields to select (default: label, length, name, type, calculated, unique)
     *
     * @return array
     *
     * @link describeObject
     */
    public function getObjectFields(string $object, array $additionalSelects = []): array
    {
        $toSelect = array_merge(['label', 'length', 'name', 'type', 'calculated', 'unique'], $additionalSelects);
        $objectMetadata = $this->describeObject($object);

        return array_map(function ($field) use ($toSelect) {
            return array_intersect_key($field, array_flip($toSelect));
        }, $objectMetadata['fields']);
    }

    /**
     * Return a list of recently deleted records for a given SObject. Start and End must be valid datetime in UTC.
     *
     * @param string $object object to query. Include __c for custom objects.
     * @param string $start  start date, in UTC
     * @param string $end    end date, in UTC
     *
     * @return array|mixed
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_getdeleted.htm
     */
    public function getDeletedRecords(string $object, string $start, string $end)
    {
        $request = new GetDeletedRecords($object);
        $request->setQuery([
            'start' => $start,
            'end'   => $end,
        ]);

        return $this->executeRequest($request);
    }
}
