<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\CreateJob;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\GetJob;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\GetJobResults;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\UpdateJobState;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\UploadJobData;
use myoutdeskllc\SalesforcePhp\SalesforceApi;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;

class BulkApi2 extends SalesforceApi
{
    /**
     * Creates an bulk api job using the bulk api 2.0.
     *
     * @param string $object salesforce object to create
     * @param string $delimiter this must be the delimiter you wish to use with the CSV file
     * @param string $lineEnding this must match your OS if you are not using a reader such as PHPLeagueCSV
     * @param string $operation These can be found in the BulkApiConstants
     */
    public function createJobDirectly(string $object, string $delimiter, string $lineEnding, string $operation): array
    {
        $request = new CreateJob();
        $request->body()->set([
            'columnDelimiter' => $delimiter,
            'lineEnding' => $lineEnding,
            'object' => $object,
            'operation' => $operation,
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Returns information about the salesforce job from the Bulk API 2.0.
     *
     * @param string $jobId salesforce ID of the job
     *
     * @return array *
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_asynch.meta/api_asynch/get_job_info.htm
     */
    public function getJobDirectly(string $jobId): array
    {
        $request = new GetJob($jobId);

        return $this->executeRequest($request);
    }

    /**
     * Uploads data to a job directly. resourceStream should be some sort of stream interface or string CSV.
     *
     * @param string $jobId salesforce ID of the job
     * @param $resourceStream
     *
     * @return int status code returned from salesforce. Should be 201 for success.
     */
    public function uploadJobDataDirectly(string $jobId, $resourceStream): int
    {
        $request = new UploadJobData($jobId, $resourceStream);

        return $this->executeRequestDirectly($request)->status();
    }

    /**
     * Closes a job, marking it ready for processing by Salesforce.
     *
     * @param string $jobId salesforce ID of the job
     *
     * @return array
     */
    public function closeJobDirectly(string $jobId): array
    {
        $request = new UpdateJobState($jobId, BulkApiOptions::UPLOAD_COMPLETE);

        return $this->executeRequest($request);
    }

    /**
     * Aborts a job that has not yet been started.
     *
     * @param string $jobId salesforce ID of the job
     */
    public function abortJobDirectly(string $jobId): array
    {
        $request = new UpdateJobState($jobId, BulkApiOptions::ABORT);

        return $this->executeRequest($request);
    }

    /**
     * Returns an array of records that successfully completed their operation.
     *
     * @param string $jobId salesforce ID of the job
     */
    public function getSuccessfulRecordsDirectly(string $jobId): array
    {
        $request = new GetJobResults($jobId, BulkApiOptions::SUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }

    /**
     * Returns a list of records that failed their operation.
     *
     * @param string $jobId salesforce ID of the job
     */
    public function getFailedRecordsDirectly(string $jobId): array
    {
        $request = new GetJobResults($jobId, BulkApiOptions::UNSUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }

    /**
     * Creates a job in Salesforce via the given SalesforceJob helper class.
     *
     * @param SalesforceJob $salesforceJob
     *
     */
    public function createJob(SalesforceJob $salesforceJob): array
    {
        $request = new CreateJob();
        $request->body()->set([
            'columnDelimiter' => $salesforceJob->getDelimiter(),
            'lineEnding' => $salesforceJob->getLineEnding(),
            'object' => $salesforceJob->getObject(),
            'operation' => $salesforceJob->getOperation(),
        ]);

        return $this->executeRequest($request);
    }

    /**
     * Returns an instance of the SalesforceJob class with properties set.
     *
     * @param SalesforceJob $salesforceJob instance of SalesforceJob, with ID set
     *
     * @return array
     */
    public function getJob(SalesforceJob $salesforceJob): array
    {
        $request = new GetJob($salesforceJob->getJobId());

        return $this->executeRequest($request);
    }

    /**
     * @param SalesforceJob $salesforceJob instance of SalesforceJob, with ID set
     *
     * @return int
     */
    public function uploadJobData(SalesforceJob $salesforceJob): int
    {
        $request = new UploadJobData($salesforceJob->getJobId(), $salesforceJob->getUploadStream());

        return $this->executeRequestDirectly($request)->status();
    }

    /**
     * Closes the job, marking it ready for processing inside salesforce.
     *
     * @param SalesforceJob $salesforceJob instance of SalesforceJob, with ID set
     *
     */
    public function closeJob(SalesforceJob $salesforceJob): array
    {
        $request = new UpdateJobState($salesforceJob->getJobId(), BulkApiOptions::UPLOAD_COMPLETE);

        return $this->executeRequest($request);
    }

    /**
     * Marks a job as aborted, abandoning any batches.
     *
     * @param SalesforceJob $salesforceJob instance of SalesforceJob, with ID set
     *
     */
    public function abortJob(SalesforceJob $salesforceJob): array
    {
        $request = new UpdateJobState($salesforceJob->getJobId(), BulkApiOptions::ABORT);

        return $this->executeRequest($request);
    }

    /**
     * Returns records successfully processed for the given job.
     *
     * @param SalesforceJob $salesforceJob instance of SalesforceJob, with ID set
     *
     */
    public function getSuccessfulRecords(SalesforceJob $salesforceJob): array
    {
        $request = new GetJobResults($salesforceJob->getJobId(), BulkApiOptions::SUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }

    /**
     * Returns records that failed to process for the given job.
     *
     * @param SalesforceJob $salesforceJob instance of SalesforceJob, with ID set
     *
     * @return array
     */
    public function getFailedRecords(SalesforceJob $salesforceJob): array
    {
        $request = new GetJobResults($salesforceJob->getJobId(), BulkApiOptions::UNSUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }
}
