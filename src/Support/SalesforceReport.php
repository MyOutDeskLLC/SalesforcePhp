<?php

namespace myoutdeskllc\SalesforcePhp\Support;

/**
 * TODO: Reports are huge, having a helper decode the fact maps would be a massive help.
 */
class SalesforceReport
{
    protected string $id;
    protected string $folderId;
    protected string $developerName;
    protected string $readableName;

    protected array $filters;
}
