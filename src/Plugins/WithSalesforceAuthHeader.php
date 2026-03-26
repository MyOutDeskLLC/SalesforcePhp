<?php

namespace myoutdeskllc\SalesforcePhp\Plugins;

use myoutdeskllc\SalesforcePhp\SalesforceApi;

trait WithSalesforceAuthHeader
{
    public function bootWithSalesforceAuthHeader(): void
    {
        $this->headers()->merge([
            'Authorization' => 'Bearer '.SalesforceApi::token(),
        ]);
    }
}
