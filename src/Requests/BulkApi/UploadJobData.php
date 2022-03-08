<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use myoutdeskllc\SalesforcePhp\Connectors\SalesforceConnector;
use League\Csv\AbstractCsv;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Traits\Plugins\HasBody;
use Sammyjo20\Saloon\Traits\Plugins\HasJsonBody;
use Sammyjo20\Saloon\Traits\Plugins\HasMultipartBody;

class UploadJobData extends \Sammyjo20\Saloon\Http\SaloonRequest
{
    use HasBody;

    protected ?string $jobId = null;
    protected ?string $method = Saloon::PUT;
    protected ?string $connector = SalesforceConnector::class;
    protected ?AbstractCsv $stream = null;

    public function __construct(string $jobId, AbstractCsv $stream)
    {
        $this->jobId = $jobId;
        $this->stream = $stream;
    }

    public function defineEndpoint(): string
    {
        return "/jobs/ingest/{$this->jobId}/batches";
    }

    public function defineBody(): string
    {
        return $this->stream->toString();
    }

    public function defaultHeaders(): array
    {
        return ['Content-Type' => 'text/csv'];
    }
}