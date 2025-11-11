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

namespace Sugarcrm\Sugarcrm\Reports\Traits;

trait ReportHelper
{
    public static $DEFAULT_LIST_VIEW_NO_PER_PAGE = 50;

    /**
     * Get Next Offset
     *
     * @param number $limit
     * @param number $offset
     * @param number $totalCount
     *
     * @return int
     */
    public function getNextOffset($limit, $offset, $totalCount): int
    {
        $nextOffset = -1;

        if ($offset < 0) {
            $offset = 0;
        }

        $nextOffset = $offset + (int)$limit;

        if ($nextOffset >= $totalCount) {
            $nextOffset = -1;
        }

        return $nextOffset;
    }

    /**
     * Gets offset and limit for pagination.
     *
     * @param array $args The arguments array passed in from the API containing the module and the record
     *
     * @return array
     */
    public function getPagination(array $args): array
    {
        $offset = 0;
        $limit = -1;
        $defaultLimit = self::$DEFAULT_LIST_VIEW_NO_PER_PAGE;

        if (isset($args['offset'])) {
            $offset = (int)$args['offset'];
        }
        if ($offset < 0) {
            $offset = 0;
        }
        if (isset($args['maxNum']) && $args['maxNum'] !== '') {
            $limit = (int)$args['maxNum'];
        }

        if (!$limit || $limit < 1 || $limit > $defaultLimit) {
            $limit = $defaultLimit;
        }

        return [
            $offset,
            $limit,
        ];
    }

    /**
     * Get total number of pages.
     *
     * @param $args array The arguments array passed in from the API containing the module and the record
     *
     * @return array
     */
    public function getTotalPagesNumber($totalRecords, $itemsPerPage): int
    {
        return (int)ceil($totalRecords / $itemsPerPage);
    }

    /**
     * If we have a custom field we have to update the table alias
     *
     * @param array $layoutDef
     * @param array $fieldDef
     */
    public function resolveCustomField(array &$layoutDef, array $fieldDef)
    {
        if (!array_key_exists('source', $fieldDef)) {
            return;
        }

        $fieldDefSource = $fieldDef['source'];

        if (!empty($fieldDefSource) && ($fieldDefSource === 'custom_fields' || ($fieldDefSource === 'non-db'
                    && !empty($fieldDef['ext2']) && !empty($fieldDef['id']))) && !empty($fieldDef['real_table'])
        ) {
            $layoutDef['table_alias'] .= '_cstm';
        }
    }


    /**
     * Since the labels can be edited manually by user during the create/edit process of report
     * we have to be sure that we're sending the correct label
     *
     * @param null|string $labelDef
     * @param null|string $labelValue
     * @param null|string $module
     *
     * @return array
     */
    public function getFieldLabel(?string $labelDef, ?string $labelValue, ?string $module): array
    {
        $translatedValue = $module ? translate($labelDef, $module) : translate($labelDef);

        $labelData = [
            'translated' => true,
            'value' => $labelValue,
        ];

        if ($translatedValue === $labelValue) {
            $labelData = [
                'translated' => false,
                'value' => $labelDef,
            ];
        }

        return $labelData;
    }
}
