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
     * Return basic information about the Object
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_basic_info.htm
     */
    public function getObjectInformation(string $object)
    {
        $request = new GetObjectInformation($object);

        return $this->executeRequest($request);
    }

    /**
     * Return basic information about all objects available to this instance
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_describeGlobal.htm
     */
    public function listObjects()
    {
        $request = new DescribeGlobal();

        return $this->executeRequest($request);
    }

    /**
     * Return full metadata information about the object
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_describe.htm
     */
    public function describeObject(string $object)
    {
        $request = new DescribeObject($object);

        return $this->executeRequest($request);
    }

    /**
     * Returns object fields, but with limited information that is more easily understandable for serialization, etc
     *
     * @link describeObject
     */
    public function getObjectFields(string $object, array $additionalSelects = [])
    {
        $toSelect = array_merge(['label', 'length', 'name', 'type', 'calculated', 'unique'], $additionalSelects);
        $objectMetadata = $this->describeObject($object);

        return array_map(function($field) use ($toSelect) {
            return array_intersect_key($field, array_flip($toSelect));
        }, $objectMetadata['fields']);
    }

    /**
     * Return a list of recently deleted records for a given SObject. Start and End must be valid datetime in UTC
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_getdeleted.htm
     */
    public function getDeletedRecords(string $object, string $start, string $end)
    {
        $request = new GetDeletedRecords($object);
        $request->setQuery([
            'start' => $start,
            'end' => $end
        ]);

        return $this->executeRequest($request);
    }
}