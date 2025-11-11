<?php

/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

use Google\Service\PeopleService\Biography;
use Google\Service\PeopleService\Birthday;
use Google\Service\PeopleService\ListConnectionsResponse;
use Google\Service\PeopleService\Name;
use Google\Service\PeopleService\Organization;

/**
 * Class ext_eapm_google
 */
class ext_eapm_google extends source
{
    protected $_enable_in_wizard = false;
    protected $_enable_in_hover = false;
    protected $_has_testing_enabled = false;

    /** {@inheritdoc} */
    public function getItem($args = [], $module = null)
    {
    }

    /** {@inheritdoc} */
    public function getList($args = [], $module = null)
    {
        $rows = [];
        $total = 0;
        $nextPageToken = null;

        try {
            /** @var Google\Client $client */
            $client = $this->_eapm->getClient();
            $service = new Google\Service\PeopleService($client);

            do {
                /** @var ListConnectionsResponse $result */
                $result = $service->people_connections->listPeopleConnections(
                    'people/me',
                    [
                        'personFields' => 'names,organizations,birthdays,biographies,emailAddresses,addresses,phoneNumbers',
                        'pageSize' => 1000,
                        'pageToken' => $nextPageToken,
                    ]
                );

                foreach ($result->getConnections() as $item) {
                    $rows[] = $this->formatPerson($item);
                }
                $total = $result->getTotalItems();
                $nextPageToken = $result->getNextPageToken();
            } while ($nextPageToken !== null);
        } catch (\Exception $e) {
            $GLOBALS['log']->fatal('Unable to retrieve item list for google contact connector: ' . $e->getMessage());
            return false;
        }

        return [
            'totalResults' => $total,
            'records' => $rows,
        ];
    }

    protected function formatPerson(\Google\Service\PeopleService\Person $person)
    {
        $result = [];
        /** @var Name $name */
        $name = $this->getPrimaryOrFirst($person->getNames());
        if ($name !== null) {
            $result['first_name'] = $name->getGivenName();
            $result['last_name'] = $name->getFamilyName();
        }
        /** @var Birthday $birthday */
        $birthday = $this->getPrimaryOrFirst($person->getBirthdays());
        if ($birthday !== null) {
            $result['birthday'] = sprintf('%d-%d-%d', $birthday->getDate()->getYear(), $birthday->getDate()->getMonth(), $birthday->getDate()->getDay());
        }
        /** @var Biography $bio */
        $bio = $this->getPrimaryOrFirst($person->getBiographies());
        if ($bio !== null) {
            $result['notes'] = $bio->getValue();
        }
        /** @var Organization $organization */
        $organization = $this->getPrimaryOrFirst($person->getOrganizations());
        if ($organization !== null) {
            $result['title'] = $organization->getTitle();
        }

        foreach ($person->getEmailAddresses() as $emailAddress) {
            if (!isset($result['email1']) && $emailAddress->getMetadata()->getPrimary()) {
                $result['email1'] = $emailAddress->getValue();
                continue;
            }
            if (!isset($result['email2'])) {
                $result['email2'] = $emailAddress->getValue();
            }
        }

        $phoneTypeMap = [
            'work' => 'phone_work',
            'home' => 'phone_home',
            'workFax' => 'phone_fax',
            'mobile' => 'phone_mobile',
            'main' => 'phone_other',
        ];
        $firstNoLabel = null;
        foreach ($person->getPhoneNumbers() as $phoneNumber) {
            $key = $phoneTypeMap[$phoneNumber->getType()] ?? null;
            if ($key === null) {
                if ($firstNoLabel === null) {
                    $firstNoLabel = $phoneNumber->getCanonicalForm() ?? $phoneNumber->getValue();
                }
                continue;
            }
            if (!isset($result[$key])) {
                $result[$key] = $phoneNumber->getCanonicalForm() ?? $phoneNumber->getValue();
            }
        }
        if (!isset($result['phone_work']) && $firstNoLabel !== null) {
            $result['phone_work'] = $firstNoLabel;
        }

        $primaryAddress = $altAddress = null;
        foreach ($person->getAddresses() as $address) {
            if (!isset($primaryAddress) && $address->getMetadata()->getPrimary()) {
                $primaryAddress = $address;
                continue;
            }
            if (!isset($altAddress)) {
                $altAddress = $address;
            }
        }
        if ($primaryAddress !== null) {
            $result['primary_address_street'] = $primaryAddress->getStreetAddress();
            $result['primary_address_postalcode'] = $primaryAddress->getPostalCode();
            $result['primary_address_city'] = $primaryAddress->getCity();
            $result['primary_address_state'] = $primaryAddress->getRegion();
            $result['primary_address_country'] = $primaryAddress->getCountry();
        }
        if ($altAddress !== null) {
            $result['alt_address_street'] = $altAddress->getStreetAddress();
            $result['alt_address_postalcode'] = $altAddress->getPostalCode();
            $result['alt_address_city'] = $altAddress->getCity();
            $result['alt_address_state'] = $altAddress->getRegion();
            $result['alt_address_country'] = $altAddress->getCountry();
        }

        return $result;
    }

    protected function getPrimaryOrFirst(array $items): ?Google\Model
    {
        if (count($items) === 0) {
            return null;
        }
        foreach ($items as $item) {
            if ($item->getMetadata()->getPrimary()) {
                return $item;
            }
        }
        return reset($items);
    }
}
