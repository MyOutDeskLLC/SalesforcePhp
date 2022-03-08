<?php

namespace myoutdeskllc\SalesforcePhp\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class SalesforceResourceOwner implements ResourceOwnerInterface
{
    protected array $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * Get the connected users salesforce id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->getResponseData('user_id');
    }

    /**
     * Get user first name.
     *
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->getResponseData('first_name');
    }

    /**
     * Get user last name.
     *
     * @return string
     */
    public function getLastName() : string
    {
        return $this->getResponseData('last_name');
    }

    /**
     * Get user email.
     *
     * @return string
     */
    public function getEmail() : string
    {
        return $this->getResponseData('email');
    }

    /**
     * Returns the instance URL
     *
     * @return string
     */
    public function getInstanceUrl() : string
    {
        return $this->getResponseData('instance_url');
    }

    /**
     * Returns the response as an array
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->response;
    }

    /**
     * Returns data from the response
     *
     * @param $path
     * @param $default
     * @return array|mixed|null
     */
    protected function getResponseData($path, $default = null): mixed
    {
        $array = $this->response;

        if (! empty($path)) {
            $keys = explode('.', $path);

            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }
}