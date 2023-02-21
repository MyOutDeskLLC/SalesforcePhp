<?php

namespace myoutdeskllc\SalesforcePhp\Plugins;

use myoutdeskllc\SalesforcePhp\SalesforceApi;

trait WithSalesforceAuthHeader
{
    public function bootWithSalesforceAuthHeader(): void
    {
        $this->mergeHeaders([
            'Authorization' => 'Bearer ' . SalesforceApi::token(),
        ]);
    }
}
