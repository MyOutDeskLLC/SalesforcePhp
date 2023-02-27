<?php

namespace myoutdeskllc\SalesforcePhp\Support;

use InvalidArgumentException;
use League\Csv\AbstractCsv;
use League\Csv\Reader;
use League\Csv\Writer;
use myoutdeskllc\SalesforcePhp\Api\BulkApi2;
use myoutdeskllc\SalesforcePhp\Constants\BulkApiOptions;
use Psr\Http\Message\StreamInterface;

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

    // This class needs a reference to an authenticated API to run most functions
    protected ?BulkApi2 $api = null;

    /**
     * Construct a new instance of the job helper class. This class can interface with the API directly if you pass in an instance.
     *
     * @param BulkApi2|null $api an authenticated instance of the BulkApi2 connector
     */
    public function __construct(?BulkApi2 $api = null)
    {
        $this->api = $api;
    }

    /**
     * Helper method to make a job, upload data, and close the job immediately.
     *
     * @param string   $object  the object to create
     * @param string   $csvFile the CSV file path to upload
     * @param BulkApi2 $api     instance of the Bulk API to use
     *
     * @return SalesforceJob
     */
    public static function createJobAndUploadCsv(string $object, string $csvFile, BulkApi2 $api): self
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
     * If this job has not been initialized, will attempt to create it.
     *
     * @return void
     */
    public function initJob(): void
    {
        if ($this->api === null) {
            throw new InvalidArgumentException('API has not been set');
        }
        if ($this->id !== null) {
            throw new InvalidArgumentException('Job has already been created');
        }
        if ($this->object === null) {
            throw new InvalidArgumentException('Object has not been set');
        }

        $this->setDataFromApiResponse($this->api->createJob($this));
    }

    /**
     * Uploads the data to salesforce.
     *
     * @return bool true if the upload was successful (we got status code 201)
     */
    public function upload(): bool
    {
        return $this->api->uploadJobData($this) === 201;
    }

    /**
     * Initializes and creates an instance of this class based on data from salesforce. Useful for getting existing job status'.
     *
     * @param string   $id  ID of the salesforce job
     * @param BulkApi2 $api instance of the Bulk API to use
     *
     * @return SalesforceJob
     */
    public static function getExistingJobById(string $id, BulkApi2 $api): self
    {
        $self = new self($api);
        $self->setId($id);
        $self->setDataFromApiResponse($api->getJob($self));

        return $self;
    }

    /**
     * if you want to check the status of a job, set its ID here.
     *
     * @param string $id salesforce if of the job
     *
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Returns the line ending type for the underlying data.
     *
     * @return string
     */
    public function getLineEnding(): string
    {
        return $this->lineEnding;
    }

    /**
     * This is set by default to LINEFEED.
     *
     * @param string $returnType
     *
     * @return void
     */
    public function setLineEnding(string $returnType): void
    {
        $this->lineEnding = $returnType;
    }

    /**
     * Returns the object this job is interacting with, if set.
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * Sets the salesforce object to upload against (custom or built in).
     *
     * @param string $object sets the object to perform bulk operations on (insert, update, delete, etc)
     *
     * @return void
     */
    public function setObject(string $object): void
    {
        $this->object = $object;
    }

    /**
     * Returns the operation (by default, insert).
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * Sets the operation (by default, insert).
     *
     * Reference BulkApiConstants for available operation types
     *
     * @param string $operation
     *
     * @return void
     */
    public function setOperation(string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * Returns the URL (only available when the JOB is in open state).
     *
     * @return string|null
     */
    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    /**
     * Sets the instance of the bulk api to use.
     *
     * @param BulkApi2 $api
     *
     * @return void
     */
    public function setApi(BulkApi2 $api): void
    {
        $this->api = $api;
    }

    /**
     * Returns the job state, if available.
     *
     * One of: Open, UploadComplete, JobComplete, Aborted, Failed
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Returns the number of records processed
     *
     * @return int|null
     */
    public function getNumberOfRecordsProcessed(): ?int
    {
        return $this->numberOfRecordsProcessed;
    }

    /**
     * Returns the number of records failed
     *
     * @return int|null
     */
    public function getNumberOfRecordsFailed(): ?int
    {
        return $this->numberOfRecordsFailed;
    }

    /**
     * Returns an instance of the underlying data stream (Abstract CSV).
     *
     * @return AbstractCsv|null
     */
    public function getUploadStream(): ?AbstractCsv
    {
        return $this->stream;
    }

    /**
     * Returns the salesforce ID of the job.
     *
     * @return string|null
     */
    public function getJobId(): ?string
    {
        return $this->id;
    }

    /**
     * Returns data on successful operations.
     */
    public function getSuccessfulResults(): StreamInterface
    {
        return $this->api->getSuccessfulRecords($this);
    }

    /**
     * Returns data from salesforce on failed records.
     */
    public function getFailedResults(): StreamInterface
    {
        return $this->api->getFailedRecords($this);
    }

    public function getSuccessfulResultsAsArray()
    {
        $csvStream = $this->getSuccessfulResults();
        $reader = Reader::createFromString($csvStream);
        $results = [];
        foreach($reader as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function getFailedResultsAsArray()
    {
        $csvStream = $this->getFailedResults();
        $reader = Reader::createFromString($csvStream);
        $results = [];
        foreach($reader as $row) {
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Aborts the bulk data job.
     *
     * @return $this
     */
    public function abortJob(): self
    {
        $this->setDataFromApiResponse($this->api->abortJob($this));

        return $this;
    }

    /**
     * Parses the response from Salesforce and updates this object as necessary.
     *
     * @param array $apiResponse API response from salesforce bulk api 2.0
     *
     * @return void
     */
    public function setDataFromApiResponse(array $apiResponse): void
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
     * Helper method to upload bulk records quickly, directly.
     *
     * @param array $records
     *
     * @return self
     */
    public function uploadRecordsBulk(array $records): self
    {
        $this->closeExistingStream();
        $this->setRecordsToUpload($records);
        $this->api->uploadJobData($this);
        $this->closeJob();

        return $this;
    }

    /**
     * Frees an existing stream, if one is opened.
     *
     * @return void
     */
    protected function closeExistingStream(): void
    {
        $this->stream = null;
    }

    /**
     * If you want to bulk upload records from a CSV, pass them in here.
     * Be sure the first item in the list of records is the header for the columns.
     *
     * @return $this
     */
    public function setRecordsToUpload(array $records): self
    {
        $this->closeExistingStream();
        $writer = Writer::createFromString();
        $writer->insertAll($records);
        $this->stream = $writer;

        return $this;
    }

    /**
     * Closes the job, marking it as upload ready (salesforce will begin processing it).
     *
     * @return $this
     */
    public function closeJob(): self
    {
        $this->setDataFromApiResponse($this->api->closeJob($this));

        return $this;
    }

    /**
     * Helper method to upload a file stream directly.
     *
     * @param resource $streamToCsvFile stream to valid CSV data (via fopen, url, etc)
     *
     * @return self
     */
    public function uploadFileStreamAndClose($streamToCsvFile): self
    {
        $this->closeExistingStream();
        $this->setFileStream($streamToCsvFile);
        $this->api->uploadJobData($this);
        $this->closeJob();

        return $this;
    }

    /**
     * If you have your own file stream, you can supply it here.
     *
     * @param resource $stream file stream
     *
     * @return $this
     */
    public function setFileStream($stream): self
    {
        $this->stream = Reader::createFromStream($stream);
        $this->delimiter = $this->stream->getDelimiter();

        return $this;
    }

    /**
     * Returns the type of line ending to be used for CSV uploads.
     *
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * This is set automatically by the CSV reader, no need to set this.
     *
     * @param string $delimiter
     *
     * @return void
     */
    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Helper method that uploads a CSV immediately.
     *
     * @param string $filePath readable path to a CSV file
     *
     * @return self
     */
    public function uploadCsvFileAndClose(string $filePath): self
    {
        $this->closeExistingStream();
        $this->setCsvFile($filePath);
        $this->api->uploadJobData($this);
        $this->closeJob();

        return $this;
    }

    /**
     * If you want to just supply a file path, do so here.
     *
     * @param string $csvFile
     *
     * @return $this
     */
    public function setCsvFile(string $csvFile): self
    {
        $this->closeExistingStream();
        $this->stream = Reader::createFromPath($csvFile);

        return $this;
    }

    /**
     * Refreshes this job, querying salesforce again to get the current status.
     *
     * @return void
     */
    public function refreshStatus(): void
    {
        $this->setDataFromApiResponse($this->api->getJob($this));
    }
}
