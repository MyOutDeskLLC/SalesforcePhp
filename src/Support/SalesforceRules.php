<?php

namespace myoutdeskllc\SalesforcePhp\Support;

class SalesforceRules
{
    /**
     * Returns regex validation to check folder api names generated.
     *
     * @return string to be used for regex testing api folder names
     */
    public static function getFolderNameValidation(): string
    {
        return '/^([a-zA-Z])(?!\w*__)\w+?\w*(?<!_)$/m';
    }
}
