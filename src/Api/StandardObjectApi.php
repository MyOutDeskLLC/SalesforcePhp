<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use Carbon\Carbon;
use myoutdeskllc\SalesforcePhp\Constants\StandardObjects;
use myoutdeskllc\SalesforcePhp\Requests\Attachment\CreateAttachment;
use myoutdeskllc\SalesforcePhp\SalesforceApi;

class StandardObjectApi extends SalesforceApi
{
    public function createLead(array $leadToCreate)
    {
        return $this->createRecord(StandardObjects::OBJECT_Lead, $leadToCreate);
    }

    public function createLeads(array $leadsToCreate)
    {
        return $this->createRecords(StandardObjects::OBJECT_Lead, $leadsToCreate);
    }

    public function getLead(string $id, array $fields)
    {
        return $this->getRecord(StandardObjects::OBJECT_Lead, $id, $fields);
    }

    public function getLeads(array $ids, array $fields)
    {
        return $this->getRecords(StandardObjects::OBJECT_Lead, $ids, $fields);
    }

    public function createOpportunity(array $opportunityInformation)
    {
        return $this->createRecord(StandardObjects::OPPORTUNITY, $opportunityInformation);
    }

    public function createOpportunities(array $opportunitiesToCreate)
    {
        return $this->createRecords(StandardObjects::OPPORTUNITY, $opportunitiesToCreate);
    }

    public function getOpportunity(string $id, array $fields)
    {
        return $this->getRecord(StandardObjects::OPPORTUNITY, $id, $fields);
    }

    public function getOpportunities(array $ids, array $fields)
    {
        return $this->getRecords(StandardObjects::OPPORTUNITY, $ids, $fields);
    }

    public function createAccount(array $accountInformation)
    {
        return $this->createRecord(StandardObjects::ACCOUNT, $accountInformation);
    }

    public function createAccounts(array $accountsToCreate)
    {
        return $this->createRecords(StandardObjects::ACCOUNT, $accountsToCreate);
    }

    public function getAccount(string $id, array $fields)
    {
        return $this->getRecord(StandardObjects::ACCOUNT, $id, $fields);
    }

    public function getAccounts(array $ids, array $fields)
    {
        return $this->getRecords(StandardObjects::ACCOUNT, $ids, $fields);
    }

    public function getContact(string $id, array $fields)
    {
        return $this->getRecord(StandardObjects::CONTACT, $id, $fields);
    }

    public function getContacts(array $ids, array $fields)
    {
        return $this->getRecords(StandardObjects::CONTACT, $ids, $fields);
    }

    /**
     * Creates an attachment in Salesforce using the base64 encoded body of the file (buffer)
     *
     * @param string $parentObjectId object this attaches to under notes & attachments
     * @param string $name name of the attachment in salesforce
     * @param string $contentType mime type, aka: application/pdf, image/jpeg, etc
     * @param string $description used for information
     * @param resource $attachmentBody should be a resource, stream, buffer of the file. Not a path.
     */
    public function createAttachment(string $parentObjectId, string $name, string $contentType, string $description, $attachmentBody)
    {
        $request = new CreateAttachment();

        $request->setData([
            'ParentId'    => $parentObjectId,
            'Name'        => $name,
            'Body'        => base64_encode($attachmentBody),
            'ContentType' => $contentType,
            'Description' => $description,
        ]);

        return $this->executeRequest($request);
    }
}