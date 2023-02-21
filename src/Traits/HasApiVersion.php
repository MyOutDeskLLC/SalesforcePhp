<?php

namespace myoutdeskllc\SalesforcePhp\Traits;

trait HasApiVersion
{
    protected ?string $apiVersion;

    public function setApiVersion(string $apiVersion): void
    {
        $this->apiVersion = 'v' . str_replace($apiVersion, 'v', '');
    }

    public function useProduction(): void
    {
        $this->sandbox = false;
    }

    public function useSandbox(): void
    {
        $this->sandbox = true;
    }
}