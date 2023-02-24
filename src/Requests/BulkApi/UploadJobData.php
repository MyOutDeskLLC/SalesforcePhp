<?php

namespace myoutdeskllc\SalesforcePhp\Requests\BulkApi;

use League\Csv\AbstractCsv;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class UploadJobData extends Request implements HasBody
{
    use \Saloon\Traits\Body\HasBody;

    protected string $jobId;
    protected AbstractCsv $stream;

    protected Method $method = Method::PUT;

    public function __construct(string $jobId, AbstractCsv $stream)
    {
        $this->jobId = $jobId;
        $this->stream = $stream;
    }

    public function resolveEndpoint(): string
    {
        return "/jobs/ingest/{$this->jobId}/batches";
    }

    public function defaultBody(): string
    {
        return $this->stream->toString();
    }

    public function defaultHeaders(): array
    {
        return ['Content-Type' => 'text/csv'];
    }
}
