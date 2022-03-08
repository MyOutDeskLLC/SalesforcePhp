<?php

namespace myoutdeskllc\SalesforcePhp\Constants;

class BulkApiOptions
{
    public const SUCCESSFUL_RESULTS = 'successfulResults';
    public const UNSUCCESSFUL_RESULTS = 'failedResults';

    public const UPLOAD_COMPLETE = 'UploadComplete';
    public const ABORT = 'Aborted';

    public const DELIMITER_BACKQUOTE = 'BACKQUOTE';
    public const DELIMITER_CARET = 'CARET';
    public const DELIMITER_COMMA = 'COMMA';
    public const DELIMITER_PIPE = 'PIPE';
    public const DELIMITER_SEMICOLON = 'SEMICOLON';
    public const DELIMITER_TAB = 'TAB';

    public const LINEFEED_ENDING = 'LF';
    public const CARRIAGE_RETURN_ENDING = 'CRLF';

    public const INSERT = 'insert';
    public const DELETE = 'delete';
    public const HARD_DELETE = 'hardDelete';
    public const UPDATE = 'update';
    public const UPSERT = 'upsert';
}
