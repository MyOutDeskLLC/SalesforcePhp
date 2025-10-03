<?php

namespace myoutdeskllc\SalesforcePhp\Plugins;

use myoutdeskllc\SalesforcePhp\SalesforceApi;

// @phpstan-ignore trait.unused
trait WithSalesforceAuthHeader
{
    public function bootWithSalesforceAuthHeader(): void
    {
        $this->mergeHeaders([
            'Authorization' => 'Bearer '.SalesforceApi::token(),
        ]);
    }
}
