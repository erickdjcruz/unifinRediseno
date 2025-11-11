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

class NotesFileApi extends FileApi
{
    public function registerApiRest()
    {
        return [
            'getFileContents' => [
                'reqType' => 'GET',
                'path' => ['Notes', '?', 'file', '?'],
                'pathVars' => ['module', 'record', '', 'field'],
                'method' => 'getFile',
                'rawReply' => true,
                'allowDownloadCookie' => true,
                'shortHelp' => 'Gets the contents of a single file related to a field for a module record.',
                'longHelp' => 'include/api/help/module_record_file_field_get_help.html',
            ],
        ];
    }

    /**
     * Override base file api zip archive builder to add attachment on the current
     * Note record if needed. This is needed if 'attachments' relationship is
     * empty, but a file is stored directly on a Note record.
     *
     * @param SugarBean $record base record with attachments
     * @param string $linkName name of multi-file relationship
     * @param string $field name of file field on related beans
     * @return array Array of SugarBeans storing files to download
     *
     * @overrides FileApi::getBeansForZipArchive
     */
    public function getBeansForZipArchive(SugarBean $record, string $linkName, string $field)
    {
        $beans = parent::getBeansForZipArchive($record, $linkName, $field);
        if (!empty($record->$field) && !in_array($record->id, array_keys($beans))) {
            $beans[$record->id] = $record;
        }
        return $beans;
    }
}
