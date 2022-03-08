<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use myoutdeskllc\SalesforcePhp\Constants\BulkApiConstants;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\CreateJob;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\GetJob;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\GetJobResults;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\UpdateJobState;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\UploadJobData;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Traits\InteractsWithSalesforceJob;

class BulkApi2 extends SalesforceApi
{
   use InteractsWithSalesforceJob;

    /**
     * Creates an bulk api job using the bulk api 2.0
     *
     * @param string $object salesforce object to create
     * @param string $delimiter this must be the delimiter you wish to use with the CSV file
     * @param string $lineEnding this must match your OS if you are not using a reader such as PHPLeagueCSV
     * @param string $operation These can be found in the BulkApiConstants
     */
   public function createJobDirectly(string $object, string $delimiter, string $lineEnding, string $operation)
   {
       $request = new CreateJob();
       $request->setData([
           'columnDelimiter' => $delimiter,
           'lineEnding' => $lineEnding,
           'object' => $object,
           'operation' => $operation
       ]);

       return $this->executeRequest($request);
   }

    /**
     * Returns information about the salesforce job from the Bulk API 2.0
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_asynch.meta/api_asynch/get_job_info.htm
     */
   public function getJobDirectly(string $jobId)
   {
       $request = new GetJob($jobId);

       return $this->executeRequest($request);
   }

    /**
     * Uploads data to a job directly. resourceStream should be some sort of stream interface or string CSV
     */
   public function uploadJobDataDirectly(string $jobId, $resourceStream): int
   {
       $request = new UploadJobData($jobId, $resourceStream);

       return $request->send()->status();
   }

    /**
     * Closes a job, marking it ready for processing by Salesforce
     */
   public function closeJobDirectly(string $jobId)
   {
       $request = new UpdateJobState($jobId, BulkApiConstants::UPLOAD_COMPLETE);

       return $this->executeRequest($request);
   }

    /**
     * Aborts a job that has not yet been started
     */
   public function abortJobDirectly(string $jobId)
   {
       $request = new UpdateJobState($jobId, BulkApiConstants::ABORT);

       return $this->executeRequest($request);
   }

    /**
     * Returns an array of records that successfully completed their operation
     */
    public function getSuccessfulRecordsDirectly(string $jobId)
    {
        $request = new GetJobResults($jobId, BulkApiConstants::SUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }


    /**
     * Returns a list of records that failed their operation
     */
    public function getFailedRecordsDirectly(string $jobId)
    {
        $request = new GetJobResults($jobId, BulkApiConstants::UNSUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }
}