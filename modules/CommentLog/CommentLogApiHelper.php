<?php

declare(strict_types=1);
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

/**
 * Class CommentLogApiHelper
 */
class CommentLogApiHelper extends SugarBeanApiHelper
{
    /**
     * @inheritDoc
     *
     * For the comment log entry, get updated names instead of using static ones
     */
    public function formatForApi(SugarBean $bean, array $fieldList = [], array $options = [])
    {
        $data = parent::formatForApi($bean, $fieldList, $options);
        if (safeInArray('entry', $fieldList)) {
            $data['entry'] = $this->formatEntryForApi($data['entry']);
        }
        return $data;
    }

    /**
     * Add record names to mention tag variables
     *
     * @param string $entry The commentlog entry
     * @return string
     */
    public function formatEntryForApi(string $entry)
    {
        // Expected tag variable format is @[module:id]
        $pattern = '/@\[([\w]+):([\d\w\-]+)\]/';
        $entry = preg_replace_callback($pattern, function ($matches) {
            $record = $this->getBean($matches[1], $matches[2], ['erased_fields' => true]);

            // Does the record still exist or does the user have access to read
            if (empty($record->id) || !$record->ACLAccess('view')
                || !$record->ACLFieldAccess('name', 'read')) {
                $name = 'LBL_NO_DATA_AVAILABLE_NO_PERIOD';
            } else {
                // Always get the up to date record name instead of using static names
                $name = $record->getRecordName();

                // Deal with erased values
                $fieldDef = $record->getFieldDefinition('name');
                $field = $this->getSugarFieldHandler($fieldDef['type']);
                if ($field->isErased($record, 'name')) {
                    $name = 'LBL_VALUE_ERASED';
                }
            }

            // Rebuild the variable with the up to date name added in
            // new format is @[module:id:name]
            return '@[' . $matches[1] . ':' . $matches[2] . ':' . $name . ']';
        }, $entry);

        return $entry;
    }

    /**
     * Wrapper for BeanFactory::getBean
     *
     * @param string $module The module
     * @param string $id The ID
     * @param array $options
     * @return SugarBean|null
     */
    public function getBean(string $module, string $id, array $options)
    {
        return BeanFactory::getBean($module, $id, $options);
    }

    /**
     * Wrapper for SugarFieldHandler::getSugarField
     *
     * @param string $type Field type
     * @return SugarFieldBase
     */
    public function getSugarFieldHandler(string $type)
    {
        return SugarFieldHandler::getSugarField($type);
    }

    /**
     * @inheritDoc
     *
     * Remove any static names from the entry
     */
    public function sanitizeSubmittedData($data)
    {
        $entry = $data['entry'];
        $entry = $this->removeNamesFromCommentEntry($entry);
        $data['entry'] = $entry;

        return $data;
    }

    /**
     * Removes the name of the record from the mention variable
     *
     * @param string $entry The commentlog entry to sanitize
     * @return string
     */
    public function removeNamesFromCommentEntry(string $entry)
    {
        if (empty($entry)) {
            return '';
        }

        $pattern = '/@\[([\w]+):([\d\w\-]+):(.+?)\]/';
        $entry = preg_replace_callback($pattern, function ($matches) {
            return '@[' . $matches[1] . ':' . $matches[2] . ']';
        }, $entry);

        return $entry;
    }
}
