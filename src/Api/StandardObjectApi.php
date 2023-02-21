<?php

namespace myoutdeskllc\SalesforcePhp\Api;

use myoutdeskllc\SalesforcePhp\Constants\StandardObjects;
use myoutdeskllc\SalesforcePhp\Requests\Attachment\CreateAttachment;
use myoutdeskllc\SalesforcePhp\SalesforceApi;

class StandardObjectApi extends SalesforceApi
{
    /**
     * Creates a lead in salesforce.
     *
     * @param array $leadToCreate array containing the minimum properties for a lead (Name, Email, etc)
     *
     * @return array
     */
    public function createLead(array $leadToCreate): array
    {
        return $this->createRecord(StandardObjects::Lead, $leadToCreate);
    }

    /**
     * Creates many leads in salesforce.
     *
     * @param array $leadsToCreate array of lead(s), containing the minimum properties for a lead (Name, Email, etc)
     *
     * @return array
     */
    public function createLeads(array $leadsToCreate): array
    {
        return $this->createRecords(StandardObjects::Lead, $leadsToCreate);
    }

    /**
     * Returns the given fields from lead.
     *
     * @param string $id salesforce id of the lead, should start with 00Q
     * @param array $fields array of fields to select
     *
     * @return array
     */
    public function getLead(string $id, array $fields): array
    {
        return $this->getRecord(StandardObjects::Lead, $id, $fields);
    }

    /**
     * Returns many leads.
     *
     * @param array $ids salesforce id(s) of the lead(s), should start with 00Q
     * @param array $fields array of fields to select
     *
     * @return array
     */
    public function getLeads(array $ids, array $fields): array
    {
        return $this->getRecords(StandardObjects::Lead, $ids, $fields);
    }

    /**
     * Creates an opportunity with the given data.
     *
     * @param array $opportunityInformation opportunity standard and custom fields, in an array
     *
     * @return array
     */
    public function createOpportunity(array $opportunityInformation): array
    {
        return $this->createRecord(StandardObjects::OPPORTUNITY, $opportunityInformation);
    }

    /**
     * Creates many opportunities with the given data.
     *
     * @param array $opportunitiesToCreate array of opportunities, with standard and custom fields
     *
     * @return array
     */
    public function createOpportunities(array $opportunitiesToCreate): array
    {
        return $this->createRecords(StandardObjects::OPPORTUNITY, $opportunitiesToCreate);
    }

    /**
     * Returns the opportunity with the given Id. 006.
     *
     * @param string $id salesforce id of the opportunity
     * @param array $fields list of fields to select
     *
     * @return array
     */
    public function getOpportunity(string $id, array $fields): array
    {
        return $this->getRecord(StandardObjects::OPPORTUNITY, $id, $fields);
    }

    /**
     * Returns opportunities from an array of the given id's.
     *
     * @param array $ids salesforce id(s) of the opportunities
     * @param array $fields list of fields to select
     *
     * @return array
     */
    public function getOpportunities(array $ids, array $fields): array
    {
        return $this->getRecords(StandardObjects::OPPORTUNITY, $ids, $fields);
    }

    /**
     * Creates an account with the given information.
     *
     * @param array $accountInformation array of standard, custom fields to set where key is the field name
     *
     * @return array
     */
    public function createAccount(array $accountInformation): array
    {
        return $this->createRecord(StandardObjects::ACCOUNT, $accountInformation);
    }

    /**
     * Creates account(s) with the given information.
     *
     * @param array $accountsToCreate array containing array's of account information
     *
     * @return array
     */
    public function createAccounts(array $accountsToCreate): array
    {
        return $this->createRecords(StandardObjects::ACCOUNT, $accountsToCreate);
    }

    /**
     * Returns an account with the given salesforce id.
     *
     * @param string $id salesforce id of the account
     * @param array $fields list of fields to select
     *
     * @return array
     */
    public function getAccount(string $id, array $fields): array
    {
        return $this->getRecord(StandardObjects::ACCOUNT, $id, $fields);
    }

    /**
     * Returns a list of accounts with the given salesforce id's.
     *
     * @param array $ids salesforce id(s) of the account(s)
     * @param array $fields list of fields to select
     *
     * @return array
     */
    public function getAccounts(array $ids, array $fields): array
    {
        return $this->getRecords(StandardObjects::ACCOUNT, $ids, $fields);
    }

    /**
     * Return a contact from salesforce.
     *
     * @param string $id the salesforce id of the contact
     * @param array $fields the fields to select
     *
     * @return array
     */
    public function getContact(string $id, array $fields): array
    {
        return $this->getRecord(StandardObjects::CONTACT, $id, $fields);
    }

    /**
     * Returns contact(s) from salesforce.
     *
     * @param array $ids the salesforce id(s) of the contacts
     * @param array $fields the fields to select
     *
     * @return array
     */
    public function getContacts(array $ids, array $fields): array
    {
        return $this->getRecords(StandardObjects::CONTACT, $ids, $fields);
    }

    /**
     * Creates an attachment in Salesforce using the base64 encoded body of the file (buffer).
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

        $request->body()->set([
            'ParentId' => $parentObjectId,
            'Name' => $name,
            'Body' => base64_encode($attachmentBody . ""),
            'ContentType' => $contentType,
            'Description' => $description,
        ]);

        return $this->executeRequest($request);
    }
}
