<?php

namespace myoutdeskllc\SalesforcePhp\Traits;

use myoutdeskllc\SalesforcePhp\Constants\BulkApiConstants;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\CreateJob;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\GetJob;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\GetJobResults;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\UpdateJobState;
use myoutdeskllc\SalesforcePhp\Requests\BulkApi\UploadJobData;
use myoutdeskllc\SalesforcePhp\Support\SalesforceJob;

/**
 * This trait defines the methods that the SalesforceJob class will call
 *
 * To not use these, just look at the class using this trait (SalesforceBulkApi)
 */
trait InteractsWithSalesforceJob
{
    public function createJob(SalesforceJob $salesforceJob)
    {
        $request = new CreateJob();
        $request->setData([
            'columnDelimiter' => $salesforceJob->getDelimiter(),
            'lineEnding' => $salesforceJob->getLineEnding(),
            'object' => $salesforceJob->getObject(),
            'operation' => $salesforceJob->getOperation()
        ]);

        return $this->executeRequest($request);
    }

    public function getJob(SalesforceJob $salesforceJob)
    {
        $request = new GetJob($salesforceJob->getJobId());

        return $this->executeRequest($request);
    }

    public function uploadJobData(SalesforceJob $salesforceJob) : int
    {
        $request = new UploadJobData($salesforceJob->getJobId(), $salesforceJob->getUploadStream());

        return $request->send()->status();
    }

    public function closeJob(SalesforceJob $salesforceJob)
    {
        $request = new UpdateJobState($salesforceJob->getJobId(), BulkApiConstants::UPLOAD_COMPLETE);

        return $this->executeRequest($request);
    }

    public function abortJob(SalesforceJob $salesforceJob)
    {
        $request = new UpdateJobState($salesforceJob->getJobId(), BulkApiConstants::ABORT);

        return $this->executeRequest($request);
    }

    public function getSuccessfulRecords(SalesforceJob $salesforceJob)
    {
        $request = new GetJobResults($salesforceJob->getJobId(), BulkApiConstants::SUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }

    public function getFailedRecords(SalesforceJob $salesforceJob)
    {
        $request = new GetJobResults($salesforceJob->getJobId(), BulkApiConstants::UNSUCCESSFUL_RESULTS);

        return $this->executeRequest($request);
    }
}