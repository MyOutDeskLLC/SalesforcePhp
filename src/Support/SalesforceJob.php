<?php

namespace myoutdeskllc\SalesforcePhp\Support;

use InvalidArgumentException;
use myoutdeskllc\SalesforcePhp\Api\BulkApi2;
use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use League\Csv\AbstractCsv;
use League\Csv\Reader;
use League\Csv\Writer;

/**
 * Helper class to assist with the creation and uploading of Salesforce Jobs. You do not need to use this class.
 */
class SalesforceJob
{
    protected ?string $id = null;
    protected ?string $object = null;
    protected ?string $operation = null;
    protected ?string $apiVersion = null;
    protected ?string $createdById = null;
    protected ?string $createdDate = null;
    protected ?string $state = null;
    protected ?string $contentUrl = null;
    protected ?string $systemModstamp = null;
    protected ?string $concurrencyMode = null;

    // These are related to the status of the job itself
    protected ?int $retries;
    protected ?string $totalProcessingTime;

    // Record information from the status
    protected ?int $numberOfRecordsFailed;
    protected ?int $numberOfRecordsProcessed;

    // This is a stream for ThePhpLeague's CSV implementation
    protected ?AbstractCsv $stream = null;

    // These will be handled internally by the CSV library
    protected string $delimiter = BulkApiOptions::DELIMITER_COMMA;
    protected string $lineEnding = BulkApiOptions::LINEFEED_ENDING;

    protected ?BulkApi2 $api = null;

    /**
     * Construct a new instance of the job helper class. This class can interface with the API directly if you pass in an instance
     *
     * @param BulkApi2|null $api
     */
    public function __construct(?BulkApi2 $api = null)
    {
        $this->api = $api;
    }

    /**
     * if you want to check the status of a job, set its ID here
     *
     * @param string $id salesforce if of the job
     * @return void
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * This is set automatically by the CSV reader, no need to set this
     *
     * @param string $delimiter
     * @return void
     */
    public function setDelimiter(string $delimiter) : void
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Returns the type of line ending to be used for CSV uploads
     *
     * @return string
     */
    public function getDelimiter() : string
    {
        return $this->delimiter;
    }

    /**
     * This is set by default to LINEFEED
     *
     * @param string $returnType
     * @return void
     */
    public function setLineEnding(string $returnType) : void
    {
        $this->lineEnding = $returnType;
    }

    /**
     * Returns the line ending type for the underlying data
     *
     * @return string
     */
    public function getLineEnding() : string
    {
        return $this->lineEnding;
    }

    /**
     * Sets the salesforce object to upload against (custom or built in)
     *
     * @param string $object
     * @return void
     */
    public function setObject(string $object) : void
    {
        $this->object = $object;
    }

    /**
     * Returns the object this job is interacting with, if set
     *
     * @return string
     */
    public function getObject() : string
    {
        return $this->object;
    }

    /**
     * Sets the operation (by default, insert)
     *
     * Reference BulkApiConstants for available operation types
     *
     * @param string $operation
     * @return void
     */
    public function setOperation(string $operation) : void
    {
        $this->operation = $operation;
    }

    /**
     * Returns the operation (by default, insert)
     *
     * @return string
     */
    public function getOperation() : string
    {
        return $this->operation;
    }

    /**
     * Returns the URL (only available when the JOB is in open state)
     *
     * @return string|null
     */
    public function getContentUrl() : ?string
    {
        return $this->contentUrl;
    }

    /**
     * Sets the instance of the bulk api to use
     *
     * @param BulkApi2 $api
     * @return void
     */
    public function setApi(BulkApi2 $api) : void
    {
        $this->api = $api;
    }

    /**
     * Returns the job state, if available
     *
     * @return string|null
     */
    public function getState() : ?string
    {
        return $this->state;
    }

    /**
     * If this job has not been initialized, will attempt to create it
     *
     * @return void|null
     */
    public function initJob()
    {
        if($this->api === null) {
            throw new InvalidArgumentException('API has not been set');
        }
        if($this->id !== null) {
            throw new InvalidArgumentException('Job has already been created');
        }

        $this->setDataFromApiResponse($this->api->createJob($this));
    }

    /**
     * If you have your own file stream, you can supply it here
     *
     * @param $stream
     * @return $this
     */
    public function setFileStream($stream)
    {
        $this->stream = Reader::createFromStream($stream);
        $this->delimiter = $this->stream->getDelimiter();

        return $this;
    }

    /**
     * If you want to bulk upload records from a CSV, pass them in here.
     * Be sure the first item in the list of records is the header for the columns
     *
     * @param array $records
     * @return $this
     */
    public function setRecordsToUpload(array $records)
    {
        $this->closeExistingStream();
        $writer = Writer::createFromString();
        $writer->insertAll($records);
        $this->stream = $writer;

        return $this;
    }

    /**
     * If you want to just supply a file path, do so here
     *
     * @param string $csvFile
     * @return $this
     */
    public function setCsvFile(string $csvFile)
    {
        $this->closeExistingStream();
        $this->stream = Reader::createFromPath($csvFile);

        return $this;
    }

    /**
     * Uploads the data to salesforce
     *
     * @return bool true if the upload was successful (we got status code 201)
     */
    public function upload()
    {
        return $this->api->uploadJobData($this) === 201;
    }

    /**
     * Returns an instance of the underlying data stream (Abstract CSV)
     *
     * @return AbstractCsv|null
     */
    public function getUploadStream() : ?AbstractCsv
    {
        return $this->stream;
    }

    /**
     * Returns the salesforce ID of the job
     *
     * @return string|null
     */
    public function getJobId() : ?string
    {
        return $this->id;
    }

    /**
     * Returns data on successful operations
     *
     * @return array
     */
    public function getSuccessfulResults() : array
    {
        return $this->api->getSuccessfulRecords($this);
    }

    /**
     * Returns data from salesforce on failed records
     *
     * @return array
     */
    public function getFailedResults() : array
    {
        return $this->api->getFailedRecords($this);
    }

    /**
     * Aborts the bulk data job
     *
     * @return $this
     */
    public function abortJob() : self
    {
        $this->setDataFromApiResponse($this->api->abortJob($this));

        return $this;
    }

    /**
     * Closes the job, marking it as upload ready (salesforce will begin processing it)
     *
     * @return $this
     */
    public function closeJob() : self
    {
        $this->setDataFromApiResponse($this->api->closeJob($this));

        return $this;
    }

    /**
     * @return void
     */
    protected function closeExistingStream(): void
    {
        if ($this->stream) {
            $this->stream->close();
        }
    }

    /**
     * Helper method to upload bulk records quickly, directly
     *
     * @param array $records
     * @return void
     */
    public function uploadRecordsBulk(array $records)
    {
        $this->closeExistingStream();
        $this->setRecordsToUpload($records);
        $this->api->uploadJobData($this);
        $this->closeJob();
    }

    /**
     * Helper method to upload a file stream directly
     *
     * @param $streamToCsvFile
     * @return void
     */
    public function uploadFileStreamAndClose($streamToCsvFile)
    {
        $this->closeExistingStream();
        $this->setFileStream($streamToCsvFile);
        $this->api->uploadJobData($this);
        $this->closeJob();
    }

    /**
     * Helper method that uploads a CSV immediately
     *
     * @param string $filePath
     * @return void
     */
    public function uploadCsvFileAndClose(string $filePath)
    {
        $this->closeExistingStream();
        $this->setCsvFile($filePath);
        $this->api->uploadJobData($this);
        $this->closeJob();
    }

    /**
     * Helper method to make a job, upload data, and close the job immediately
     *
     * @param string $object the object to create
     * @param string $csvFile the CSV file path to upload
     * @param BulkApi2 $api instance of the Bulk API to use
     * @return SalesforceJob
     */
    public static function createJobAndUploadCsv(string $object, string $csvFile, BulkApi2 $api) : self
    {
        $job = new self($api);
        $job->setObject($object);
        $job->setOperation(BulkApiOptions::INSERT);
        $job->setCsvFile($csvFile);
        $job->initJob();
        $job->upload();
        $job->closeJob();

        return $job;
    }

    /**
     * Parses the response from Salesforce and updates this object as necessary
     *
     * @param array $apiResponse
     * @return void
     */
    public function setDataFromApiResponse(array $apiResponse) : void
    {
        $this->id = $apiResponse['id'];
        $this->apiVersion = $apiResponse['apiVersion'];
        $this->createdById = $apiResponse['createdById'];
        $this->createdDate = $apiResponse['createdDate'];
        $this->object = $apiResponse['object'];
        $this->operation = $apiResponse['operation'];
        $this->state = $apiResponse['state'];
        $this->systemModstamp = $apiResponse['systemModstamp'];

        // Depending on the state of the job, these may not be set
        $this->contentUrl = $apiResponse['contentUrl'] ?? null;
        $this->retries = $apiResponse['retries'] ?? null;
        $this->totalProcessingTime = $apiResponse['totalProcessingTime'] ?? null;
        $this->numberOfRecordsFailed = $apiResponse['numberRecordsFailed'] ?? null;
        $this->numberOfRecordsProcessed = $apiResponse['numberRecordsProcessed'] ?? null;
    }

    /**
     * Initializes and creates an instance of this class based on data from salesforce. Useful for getting existing job status'
     *
     * @param string $id ID of the salesforce job
     * @param BulkApi2 $api instance of the Bulk API to use
     * @return SalesforceJob
     */
    public static function getExistingJobById(string $id, BulkApi2 $api)
    {
        $self = new self($api);
        $self->setId($id);
        $self->setDataFromApiResponse($api->getJob($self));

        return $self;
    }
}