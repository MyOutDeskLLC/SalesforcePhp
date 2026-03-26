<?php

namespace myoutdeskllc\SalesforcePhp\Plugins;

use myoutdeskllc\SalesforcePhp\SalesforceApi;

// @phpstan-ignore trait.unused
trait WithSalesforceAuthHeader
{
    public function bootWithSalesforceAuthHeader(): void
    {
        $this->headers()->merge([
            'Authorization' => 'Bearer '.SalesforceApi::token(),
        ]);
    }
}
