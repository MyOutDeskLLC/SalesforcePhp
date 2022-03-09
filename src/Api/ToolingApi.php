<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use myoutdeskllc\SalesforcePhp\Requests\Tooling\ExecuteAnonymousApex;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexClass;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexClasses;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexLog;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexLogs;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexPage;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexPages;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexTestRunResult;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\GetApexTestRunResults;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\RunApexTestsASync;
use myoutdeskllc\SalesforcePhp\Requests\Tooling\RunApexTestsSync;
use myoutdeskllc\SalesforcePhp\SalesforceApi;

class ToolingApi extends SalesforceApi
{
    /**
     * Uses SOQL to return a list of apex logs
     *
     * @return array array of apex logs
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_tooling.meta/api_tooling/tooling_api_objects_apexlog.htm
     */
    public function getApexLogs(): array
    {
        return $this->executeRequest(new GetApexLogs());
    }

    /**
     * Get a specific apex log (will start with 07L)
     *
     * @param string $logId id of the apex log to fetch
     * @return string raw log contents
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_tooling.meta/api_tooling/tooling_api_objects_apexlog.htm
     */
    public function getApexLog(string $logId): string
    {
        $request = new GetApexLog($logId);

        return $request->send()->body();
    }

    /**
     * Gets all visualforce pages
     *
     * @return array array of visualforce \ apex pages
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function getApexPages(): array
    {
        return $this->executeRequest(new GetApexPages());
    }

    /**
     * Returns information about a specific visualforce\apex page, including the body itself
     *
     * @param string $pageId
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function getApexPage(string $pageId): array
    {
        return $this->executeRequest(new GetApexPage($pageId));
    }

    /**
     * Run tests synchronously
     * Pass in an array of arrays, where each array is a declaration of the class to run (classId) with an optional set of methods (testMethods).
     *
     * @param array $testsToExecute array of test declarations ([classId: 'classId', testMethods: ['methodName']]
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function runTestsSynchronous(array $testsToExecute): array
    {
        if(!isset($testsToExecute['tests'])) {
            $testsToExecute = ['tests' => $testsToExecute];
        }
        $request = new RunApexTestsSync();
        $request->setData($testsToExecute);

        return $this->executeRequest($request);
    }

    /**
     * Run tests asynchronously
     *
     * @param array $listOfClassIds array of apex class ids
     * @return string returns the string ID of the run for checking the status later, without quotes
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function runTestsAsynchronousById(array $listOfClassIds): string
    {
        $request = new RunApexTestsASync();
        $request->setData(['classids' => implode(',', $listOfClassIds)]);

        return str_replace('"', '', $request->send()->body());
    }

    /**
     * Run tests asynchronously
     *
     * @param array $listOfClassNames array of apex test class names
     * @return string returns the string ID of the run for checking the status later,  without quotes
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function runTestsAsynchronousByClassNames(array $listOfClassNames): string
    {
        $request = new RunApexTestsASync();
        $request->setData(['classNames' => implode(',', $listOfClassNames)]);

        return str_replace('"', '', $request->send()->body());
    }

    /**
     * Executes anonymous APEX code. success key in result will be True if execution succeeded.
     *
     * This could be dangerous, so be careful.
     *
     * @param string $apexCode valid apex code. must end in ;, as normal.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonInvalidConnectorException
     */
    public function executeAnonymousApex(string $apexCode): array
    {
        $request = new ExecuteAnonymousApex();
        $request->setQuery([
            'anonymousBody' => $apexCode
        ]);

        return $this->executeRequest($request);
    }

    /**
     * List test executions and the result
     *
     * @return array array of apex test executions
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function getApexTestRunResults(): array
    {
        return $this->executeRequest(new GetApexTestRunResults());
    }

    /**
     * Returns the run results for a specific apex test run
     *
     * @param string $testRunId the ID of the test run
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     */
    public function getApexTestRunResult(string $testRunId): array
    {
        return $this->executeRequest(new GetApexTestRunResult($testRunId));
    }

    /**
     * Attempts to find the class id by name using SOQL, rather than the manifest
     *
     * @param string $apexClassName
     * @return array
     * @throws \SalesforceQueryBuilder\Exceptions\InvalidQueryException
     */
    public function getApexClassByName(string $apexClassName): array
    {
        $builder = self::getQueryBuilder();
        $builder->select(['Id', 'Name', 'Status'])
            ->from('ApexClass')
            ->where('Name', '=', $apexClassName);

        return $this->executeQuery($builder);
    }

    /**
     * Returns a list of apex classes available on the instance. (With sharing rules applied. User must have access to them)
     *
     * @return array list of ApexClasses
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_tooling.meta/api_tooling/tooling_api_objects_apexclass.htm
     */
    public function getApexClasses()
    {
        return $this->executeRequest(new GetApexClasses());
    }

    /**
     * Returns metadata for the apex class, including the content itself
     *
     * @param string $classId ID of the apex class
     * @return array metadata of the apex class
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     * @throws \Sammyjo20\Saloon\Exceptions\SaloonException
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_tooling.meta/api_tooling/tooling_api_objects_apexclass.htm
     */
    public function getApexClass(string $classId)
    {
        return $this->executeRequest(new GetApexClass($classId));
    }
}