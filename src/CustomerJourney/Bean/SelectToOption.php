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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean;

use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;

/**
 * This class have functions for Guests field on Task Templates as
 * well as for the Email address field on CJ Forms
 */
class SelectToOption
{
    private static $separator = '|';
    private static $dropdownName = 'cj_select_to_options_list';
    private static $moduleMappingDropdownName = 'cj_select_to_options_list_module_mapping';

    /**
     * Return the Parent Record of Activity, Stage and Journey
     * i.e Account/Contact/Opportunities and so on
     * @param \SugarBean $activityOrStageOrJourney
     * @return \SugarBean $parent
     */
    public static function getParentRecord(\SugarBean $activityOrStageOrJourney)
    {
        if (empty($activityOrStageOrJourney->id)) {
            return;
        }

        $parent = null;
        $module = $activityOrStageOrJourney->getModuleName();
        // stage or journey
        if ($module === 'DRI_SubWorkflows' || $module === 'DRI_Workflows') {
            try {
                $parent = $activityOrStageOrJourney->getParent();
            } catch (CJException\ParentNotFoundException $e) {
                $GLOBALS['log']->debug('Sugar Automate', __FILE__ . ' ' . __LINE__, $e->getMessage());
            }
        } elseif (in_array($module, ['Calls', 'Meetings', 'Tasks']) &&
            !empty($activityOrStageOrJourney->parent_type) &&
            !empty($activityOrStageOrJourney->parent_id)) {
            // activities
            $parent = \BeanFactory::getBean($activityOrStageOrJourney->parent_type, $activityOrStageOrJourney->parent_id);
        }
        return $parent;
    }

    /**
     * Return the Recipients Ids according the
     * cj_select_to type field
     * @param string $selectTo
     * @param \SugarBean $parentRecord
     * @return array $idsInfo
     */
    public static function getRecipients(string $selectTo = '', \SugarBean $parentRecord = null)
    {
        if (empty($selectTo) || empty($parentRecord->id)) {
            return [];
        }

        $cjSelectToOptionsList = self::getSelectToOptionList();
        if (empty($cjSelectToOptionsList)) {
            return [];
        }

        $selectToEmailAddressArray = self::getJsonDecodedData($selectTo);
        if (empty($selectToEmailAddressArray)) {
            return [];
        }

        $parentRelateFields = $parentRecord->get_related_fields();
        $idsInfo = [];

        foreach ($selectToEmailAddressArray as $selectToEmailAddress) {
            if (empty($selectToEmailAddress['id'])) {
                continue;
            }

            $moduleName = self::getModuleMappingAgainstCurrentOption($selectToEmailAddress['id']);
            if (empty($moduleName)) {
                continue;
            }

            if (isset($selectToEmailAddress['formattedIds']) && array_key_exists('formattedIds', $selectToEmailAddress)) {
                $formattedIds = explode(self::$separator, $selectToEmailAddress['formattedIds']);
            }

            if (empty($formattedIds) && $selectToEmailAddress['id'] !== 'add_all_contacts_from_parent') {
                continue;
            }

            if (!isset($idsInfo[$moduleName]) || !array_key_exists($moduleName, $idsInfo)) {
                $idsInfo[$moduleName] = [];
            }

            if (in_array($selectToEmailAddress['id'], ['specific_users', 'specific_contacts'])) {
                $idsInfo[$moduleName] = self::getFilteredArray(array_unique(array_merge($idsInfo[$moduleName], $formattedIds)));
            }

            if (in_array($selectToEmailAddress['id'], ['related_parent_users', 'related_parent_contacts'])) {
                $ids = self::getRelateFieldsInfoAgainstParent($parentRelateFields, $parentRecord, $formattedIds);
                $idsInfo[$moduleName] = self::getFilteredArray(array_unique(array_merge($idsInfo[$moduleName], $ids)));
            }

            if ($selectToEmailAddress['id'] === 'add_all_contacts_from_parent') {
                $ids = self::getAllManyToManyContactRecords($parentRecord);
                $idsInfo[$moduleName] = self::getFilteredArray(array_unique(array_merge($idsInfo[$moduleName], $ids)));
            }
        }

        return $idsInfo;
    }

    /**
     * Return value of Bean Field
     *
     * @param \SugarBean $parentRecord
     * @param string $parentRecordField
     *
     * @return string
     */
    public static function getBeanFieldValue(\SugarBean $parentRecord, string $parentRecordField)
    {
        if (empty($parentRecord) || empty($parentRecordField)) {
            return '';
        }

        $fieldValue = $parentRecord->{$parentRecordField};
        return (!empty($fieldValue)) ? $fieldValue : '';
    }

    /**
     * Return the ids of relate fields against
     * parent (Account/Contact etc)
     * @param array $parentRelateFields
     * @param \SugarBean $parentRecord
     * @param array $formattedIds
     * @return array
     */
    public static function getRelateFieldsInfoAgainstParent(array $parentRelateFields, \SugarBean $parentRecord, array $formattedIds)
    {
        $ids = [];
        $relateFieldsNames = array_keys($parentRelateFields);
        foreach ($formattedIds as $fieldName) {
            if (in_array($fieldName, $relateFieldsNames) && !empty($parentRelateFields[$fieldName]['id_name'])) {
                $fieldValue = self::getBeanFieldValue($parentRecord, $parentRelateFields[$fieldName]['id_name']);
                if (!empty($fieldValue)) {
                    $ids[] = $fieldValue;
                }
            }
        }
        return array_unique($ids);
    }

    /**
     * Return the ids of Contact for M-M relationship
     * with Parent Record
     *
     * @param \SugarBean $parentRecord
     * @return array contactIds
     */
    public static function getAllManyToManyContactRecords(\SugarBean $parentRecord)
    {
        $linkFields = [];
        foreach ($parentRecord->field_defs as $fieldDef) {
            if (isset($fieldDef['type']) && $fieldDef['type'] === 'link' && !empty($fieldDef['relationship'])) {
                $linkFields[] = $fieldDef;
            }
        }
        $contact_ids = [];
        foreach ($linkFields as $linkField) {
            if ($parentRecord->load_relationship($linkField['name'])) {
                $relationShipObject = $parentRecord->{$linkField['name']}->getRelationshipObject();
                $type = $relationShipObject->true_relationship_type ?? $relationShipObject->relationship_type;

                if ((((empty($linkField['module']) || $linkField['module'] !== 'Contacts') &&
                            ($relationShipObject->getRHSModule() === 'Contacts' ||
                                $relationShipObject->getLHSModule() === 'Contacts')) ||
                        (isset($linkField['module']) && $linkField['module'] === 'Contacts')) &&
                    ($type === 'many-to-many' || $type === 'one-to-many')) {
                    $parentRecord->{$linkField['name']}->resetLoaded();
                    $parentRecord->{$linkField['name']}->load([
                        'deleted' => 0,
                        'limit' => -1,
                    ]);

                    $contact_ids = array_merge($parentRecord->{$linkField['name']}->get(), $contact_ids);
                }
            }
        }
        return array_unique($contact_ids);
    }

    /**
     * Return the dropdown of
     * cj_select_to type field
     * @return array
     */
    public static function getSelectToOptionList()
    {
        global $app_list_strings;
        return $app_list_strings[self::$dropdownName];
    }

    /**
     * Return the mapping of module for the
     * getSelectToOptionList option
     * i.e. specific_users => Users
     * @param string $selectToEmailAddressOptionID
     * @return string
     */
    public static function getModuleMappingAgainstCurrentOption(string $selectToEmailAddressOptionID)
    {
        global $app_list_strings;
        $list = $app_list_strings[self::$moduleMappingDropdownName];
        if ($list[$selectToEmailAddressOptionID]) {
            return $list[$selectToEmailAddressOptionID];
        }
        return '';
    }

    /**
     * Return the Recipients Email Address against the
     * Recipients Ids according the cj_select_to type field
     * @param array $recipientsInfo
     * @return array $recipientsEmails
     */
    public static function getRecipientsEmails(array $recipientsInfo)
    {
        if (empty($recipientsInfo)) {
            return;
        }
        $recipientsEmails = [];
        $fromBean = \BeanFactory::newBean('EmailAddresses');
        foreach ($recipientsInfo as $module => $ids) {
            $ids = self::getFilteredArray($ids);
            $sugarQuery = self::getEmailsQuery($module, $ids, $fromBean);

            $result = $sugarQuery->execute();
            $recipientsEmails = array_merge($recipientsEmails, $result);
        }

        return $recipientsEmails;
    }

    /**
     * Prepare the email addresses query for the recipients Ids
     * @param string $module
     * @param array $ids
     * @param \SugarBean $fromBean
     * @return mixed $sugarQuery
     */
    public static function getEmailsQuery(string $module, array $ids, \SugarBean $fromBean)
    {
        if (empty($module) || empty($ids) || empty($fromBean) || !($fromBean instanceof \EmailAddress)) {
            return;
        }

        $sugarQuery = new \SugarQuery();
        $sugarQuery->from($fromBean, ['team_security' => false]);

        $join = $sugarQuery->joinTable('email_addr_bean_rel', ['alias' => 'ear', 'joinType' => 'INNER', 'linkingTable' => true]);
        $join->on()
            ->equalsField($sugarQuery->getFromAlias() . '.id', 'ear.email_address_id')
            ->equals('ear.deleted', 0)
            ->equals('ear.bean_module', $module)
            ->in('ear.bean_id', $ids);
        $sugarQuery->where()
            ->equals('opt_out', 0)
            ->equals('invalid_email', 0);
        $sugarQuery->select(['email_address', 'ear.bean_id', 'ear.bean_module']);

        return $sugarQuery;
    }

    /**
     * get the Names of recipients Ids and then
     * prepare the array for them
     * @param array $recipientsInfo
     * @return array $recipientsWithNames
     */
    public static function mapRecipientsWithTheirNames(array $recipientsInfo)
    {
        if (empty($recipientsInfo)) {
            return;
        }
        $recipientsWithNames = [];
        foreach ($recipientsInfo as $module => $ids) {
            $fromBean = \BeanFactory::newBean($module);
            $ids = self::getFilteredArray($ids);
            $sugarQuery = self::getNamesQuery($module, $ids, $fromBean);

            $result = $sugarQuery->execute();
            foreach ($result as $r) {
                $recipientsWithNames[$r['id']] = return_name($r, 'first_name', 'last_name');
            }
        }

        return $recipientsWithNames;
    }

    /**
     * Prepare the first_name and last_name query for the recipients Ids
     * @param string $module
     * @param array $ids
     * @param \SugarBean $fromBean
     * @return mixed $sugarQuery
     */
    public static function getNamesQuery(string $module, array $ids, \SugarBean $fromBean)
    {
        if (empty($module) || empty($ids) || empty($fromBean)) {
            return;
        }

        $sugarQuery = new \SugarQuery();
        $sugarQuery->from($fromBean, ['team_security' => false]);

        $sugarQuery->where()
            ->in('id', $ids);
        $sugarQuery->select(['first_name', 'last_name', 'id']);

        return $sugarQuery;
    }

    /**
     * Decode the data of the cj_select_to
     * type field
     * @param string $string
     * @return array $data
     */
    public static function getJsonDecodedData(string $string)
    {
        if (empty($string)) {
            return [];
        }

        $data = json_decode($string, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        return [];
    }


    /**
     * array_filter removes the empty indexes from array
     * @param array $arr
     * @return array filteredarray
     */
    private static function getFilteredArray(array $arr)
    {
        return array_filter($arr);
    }
}
