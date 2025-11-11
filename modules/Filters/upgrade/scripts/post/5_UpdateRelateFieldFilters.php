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

/**
 * Upgrade relate field filters
 */
class SugarUpgradeUpdateRelateFieldFilters extends UpgradeScript
{
    /**
     * Fields that need updating possibly
     *
     * @var array
     */
    private $updateFields = [
        'filter_template',
        'filter_definition',
    ];

    public $order = 5000;

    public function run()
    {
        // Only run this for instances that are coming from 9.0.0 or lower
        if (version_compare($this->from_version, '9.0.0', '<=')) {
            $this->updateFilterDefs();
        }
    }

    /**
     * Runs the update routine on ALL filter table rows
     */
    protected function updateFilterDefs()
    {
        // Start by collecting all of the filters rows within a statement
        $sql = 'SELECT
                    id, filter_template, filter_definition, module_name
                FROM
                    filters
                WHERE deleted = 0';
        $stmt = $this->db->getConnection()->executeQuery($sql);

        // For each row, investigate whether we need to update the data and, if so,
        // get it updated
        while ($row = $stmt->fetchAssociative()) {
            // Run the row through the converter. If something changed then
            // handle saving that change
            $row = $this->convertRow($row);
            if ($row['converted']) {
                $this->handleUpdate($row['converted'], ['id' => $row['id']]);
            }
        }
    }

    /**
     * Takes a single row of data from the filters table and handles
     * conversions for parts that require it
     * @param array $row Array of key values from a single row of filter data
     * @return array
     */
    protected function convertRow(array $row): array
    {
        // Marker to tell the calling method how to proceed
        $row['converted'] = [];

        // If there is no module name we can't really do anything
        if (empty($row['module_name'])) {
            return $row;
        }

        // Look at each of the fields that we need to inspect
        foreach ($this->updateFields as $field) {
            // If the field has data in it...
            if (!empty($row[$field])) {
                // Get the bean for this row of data
                $bean = $this->getBean($row['module_name']);

                // And try to convert if there is a need for it
                $result = $this->convertFilter($row[$field], $bean);

                // If what comes from the conversion is different that what
                // we started with, then we've actually converted something
                if ($result !== $row[$field]) {
                    $row['converted'][$field] = $result;
                }
            }
        }

        return $row;
    }

    /**
     * Gets a SugarBean object
     * @param string $module The module to get a bean for
     * @return SugarBean
     */
    protected function getBean(string $module): SugarBean
    {
        return BeanFactory::getBean($module);
    }

    /**
     * Handles conversion of operators from numeric to string '$equals'
     * @param string $json JSON string of definitions
     * @param SugarBean $bean The bean the filter is based on
     * @return string JSON string after being converted
     */
    public function convertFilter(string $json, SugarBean $bean): string
    {
        // Decode the row data
        $filters = json_decode($json, true);

        // Traverse each filter and if the field type requires conversion, try
        // to convert it.
        foreach ($filters as $k => $filter) {
            foreach ($filter as $field => $value) {
                if ($this->shouldConvert($bean->field_defs[$field] ?? [])) {
                    // If the match value is a string, convert it since a string
                    // filter will not contain an operator
                    if (is_string($value)) {
                        $filters[$k][$field] = ['$equals' => $value];
                    } elseif (is_array($value)) {
                        // If the field is an array, grab the first element if the
                        // key is numeric and 0. Array values can contain operators
                        // but the index would be a string, not an integer
                        $key = key($value);
                        if (is_numeric($key) && $key === 0) {
                            $filters[$k][$field] = ['$equals' => $value[0]];
                        }
                    }
                }
            }
        }

        return json_encode($filters);
    }

    /**
     * Determines if a field should be considered for conversion. Not all relate
     * fields are created equal.
     * @param array $def The field def
     * @return boolean
     */
    public function shouldConvert(array $def)
    {
        if (isset($def['type'])) {
            // OOTB relate fields
            if ($def['type'] === 'relate') {
                return true;
            }

            // Custom relate fields create two fields, and the actual field that
            // is being used for evaluation is the related id field
            if ($def['type'] === 'id') {
                if (isset($def['source']) && $def['source'] === 'custom_fields') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Processes handling of the update request
     * @param array $fields Fields for updating
     * @param array $where Fields for where filtering
     * @return boolean
     */
    protected function handleUpdate(array $fields, array $where)
    {
        $bean = $this->getBean('Filters');

        return $this->db->updateParams(
            $bean->getTableName(),
            $bean->getFieldDefinitions(),
            $fields,
            $where
        );
    }
}
