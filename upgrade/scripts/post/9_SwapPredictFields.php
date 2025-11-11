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
 * Swaps custom Predict fields for new stock versions of the same fields
 */
class SugarUpgradeSwapPredictFields extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_ALL;

    /**
     * The map defining what field replacements should occur by module
     */
    public $fieldsToReplace = [
        'Opportunities' => [
            'ai_opp_conv_score_enum_c' => 'ai_opp_conv_score_enum',
        ],
        'Leads' => [
            'ai_icp_fit_score_classification_c' => 'ai_icp_fit_score_classification',
            'ai_conv_score_classification_c' => 'ai_conv_score_classification',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldRun()) {
            $this->log('Replacing custom SugarPredict fields with stock ones...');
            $this->replacePredictFields();
            $this->log('SugarPredict field replacement complete');
        }
    }

    /**
     * Determines if this upgrader should run
     *
     * @return bool
     */
    protected function shouldRun(): bool
    {
        $isFromBelow12Ent = $this->toFlavor('ent') && version_compare($this->from_version, '12.0.0', '<');
        return $isFromBelow12Ent;
    }

    /**
     * Replaces Predict fields in Layout and Filter metadata based on $fieldsToReplace
     */
    protected function replacePredictFields()
    {
        $this->filterFieldsToReplace();
        foreach ($this->fieldsToReplace as $module => $fields) {
            $this->replacePredictFieldsInLayouts($module, $fields);
            $this->replacePredictFieldsInFilters($module, $fields);
            $this->replacePredictFieldsInPopupSearchLists($module, $fields);
            $this->swapDBValues($module, $fields);
        }
    }

    /**
     * Filters $this->fieldsToReplace to only contain fields that actually
     * exist in the instance
     */
    protected function filterFieldsToReplace()
    {
        $filteredFieldsToReplace = [];

        foreach ($this->fieldsToReplace as $module => $fields) {
            $moduleFieldsToReplace = [];
            $seed = BeanFactory::newBean($module);

            foreach ($fields as $field => $replacement) {
                if (!empty($seed->getFieldDefinition($field)) && !empty($seed->getFieldDefinition($replacement))) {
                    $moduleFieldsToReplace[$field] = $replacement;
                }
            }
            if (!empty($moduleFieldsToReplace)) {
                $filteredFieldsToReplace[$module] = $moduleFieldsToReplace;
            }
        }

        $this->fieldsToReplace = $filteredFieldsToReplace;
    }

    /**
     * Replaces the given fields in all custom layouts for the given module
     *
     * @param string $module the module name
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function replacePredictFieldsInLayouts($module, $fields)
    {
        $globVars = [
            'module' => 1,
            'client' => 3,
            'view' => 5,
        ];
        $baseLayoutsMeta = $this->getFilesInfo("modules/$module/clients/*/views/*/*.php", $globVars);
        $roleLayoutsMeta = $this->getFilesInfo("modules/$module/clients/*/views/*/roles/*.php", $globVars);
        $dropdownLayoutsMeta = $this->getFilesInfo("modules/$module/clients/*/views/*/dropdowns/*/*/*.php", $globVars);
        $moduleCustomLayouts = array_merge($baseLayoutsMeta, $roleLayoutsMeta, $dropdownLayoutsMeta);

        foreach ($moduleCustomLayouts as $customLayout) {
            $this->replacePredictFieldsInLayout($customLayout, $fields);
        }
    }

    /**
     * Replaces the given fields in the given layout
     *
     * @param array $customLayout information about the custom layout to modify
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function replacePredictFieldsInLayout($customLayout, $fields)
    {
        $module = $customLayout['module'];
        $client = $customLayout['client'];
        $view = $customLayout['view'];

        // Load the layout metadata from the file
        $viewdefs = [];
        include $customLayout['file'];
        if (empty($viewdefs[$module][$client]['view'][$view]['panels'])) {
            return;
        }
        $panels = $viewdefs[$module][$client]['view'][$view]['panels'];
        $fieldList = $this->getLayoutFieldList($panels);

        // Search the layout data for the fields and replace them if needed
        $changed = false;
        foreach ($fields as $fieldToReplace => $replacement) {
            if (!empty($fieldList[$fieldToReplace])) {
                foreach ($fieldList[$fieldToReplace] as $field) {
                    $pIndex = $field['panelIndex'];
                    $fIndex = $field['fieldIndex'];
                    $fieldDef = $field['fieldDef'];
                    if (!empty($panels[$pIndex]['fields'][$fIndex])) {
                        // Keep existing view properties (span, etc.) if the field is defined as an array
                        if (is_array($fieldDef) && $fieldDef['name'] === $fieldToReplace) {
                            $fieldDef['name'] = $replacement;
                            // Unset the label if it exists so that it will pick up from the new field's vname
                            unset($fieldDef['label']);
                        } elseif ($fieldDef === $fieldToReplace) {
                            $fieldDef = $replacement;
                        }
                        $panels[$pIndex]['fields'][$fIndex] = $fieldDef;
                        $changed = true;
                    }
                }
            }
        }

        // If the fields were replaced, write the changes to the file
        if ($changed) {
            $viewdefs[$module][$client]['view'][$view]['panels'] = $panels;
            write_array_to_file(
                "viewdefs['$module']['$client']['view']['$view']",
                $viewdefs[$module][$client]['view'][$view],
                $customLayout['file']
            );
        }
    }

    /**
     * Extract information about all fields in a layout, including their
     * panel index, field index within that panel, and their definition
     * in the layout
     *
     * @param array $panels the panels metadata from the layout
     * @return array a mapping of {field name} => {array of field layout information}
     */
    protected function getLayoutFieldList($panels)
    {
        $fields = [];
        foreach ($panels as $panelIndex => $panel) {
            if (empty($panel['fields'])) {
                continue;
            }
            foreach ($panel['fields'] as $fieldIndex => $field) {
                if (is_array($field)) {
                    if (empty($field['name'])) {
                        continue;
                    }
                    $fieldName = $field['name'];
                } else {
                    $fieldName = $field;
                }

                // It's unlikely, but still possible that the same field can
                // be on the view twice, so need to capture all instances of it
                $fields[$fieldName] = $fields[$fieldName] ?? [];
                $fields[$fieldName][] = [
                    'panelIndex' => $panelIndex,
                    'fieldIndex' => $fieldIndex,
                    'fieldDef' => $field,
                ];
            }
        }

        return $fields;
    }

    /**
     * Replaces the given fields in all custom filter list for the given
     * module. This only affects metadata-file-driven filters - it does not
     * alter filter definitions stored in the DB
     *
     * @param string $module the module name
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function replacePredictFieldsInFilters($module, $fields)
    {
        $moduleCustomFilters = $this->getFilesInfo("modules/$module/clients/*/filters/*/*.php", [
            'module' => 1,
            'client' => 3,
            'filter' => 5,
        ]);
        foreach ($moduleCustomFilters as $customFilter) {
            $this->replacePredictFieldsInFilter($customFilter, $fields);
        }
    }

    /**
     * Replaces the given fields in the given filter list
     *
     * @param array $customFilter information about the custom filter list to modify
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function replacePredictFieldsInFilter($customFilter, $fields)
    {
        $module = $customFilter['module'];
        $client = $customFilter['client'];
        $filter = $customFilter['filter'];

        // Load the filter metadata from the file
        $viewdefs = [];
        include $customFilter['file'];
        if (empty($viewdefs[$module][$client]['filter'][$filter]['fields'])) {
            return;
        }

        // Scan the filter for references to the fields to replace
        $changed = false;
        $updatedFilterFields = [];
        foreach ($viewdefs[$module][$client]['filter'][$filter]['fields'] as $filterField => $filterOpts) {
            $newFilterKey = $filterField;
            if (!empty($fields[$filterField])) {
                $newFilterKey = $fields[$filterField];
                $changed = true;
            }

            $newFilterOpts = $filterOpts;
            if (!empty($newFilterOpts['dbFields'])) {
                foreach ($newFilterOpts['dbFields'] as $idx => $dbField) {
                    if (!empty($fields[$dbField])) {
                        $newFilterOpts['dbFields'][$idx] = $fields[$dbField];
                        $changed = true;
                    }
                }
            }
            $updatedFilterFields[$newFilterKey] = $newFilterOpts;
        }

        if ($changed) {
            $viewdefs[$module][$client]['filter'][$filter]['fields'] = $updatedFilterFields;
            write_array_to_file(
                "viewdefs['$module']['$client']['filter']['$filter']",
                $viewdefs[$module][$client]['filter'][$filter],
                $customFilter['file']
            );
        }
    }

    /**
     * Replaces the given fields in all custom filter list for the given
     * module. This only affects metadata-file-driven filters - it does not
     * alter filter definitions stored in the DB
     *
     * @param string $module the module name
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function replacePredictFieldsInPopupSearchLists($module, $fields)
    {
        $moduleCustomPopupSearchLists = $this->getFilesInfo("modules/$module/metadata/popupdefs.php", [
            'module' => 1,
        ]);
        foreach ($moduleCustomPopupSearchLists as $customPopupSearchList) {
            $this->replacePredictFieldsInPopupSearchList($customPopupSearchList, $fields);
        }
    }

    /**
     * Replaces the given fields in the given popup search list
     *
     * @param array $customPopupSearchList information about the custom popup search list to modify
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function replacePredictFieldsInPopupSearchList($customPopupSearchList, $fields)
    {
        $client = $customPopupSearchList['client'];
        $type = $customPopupSearchList['type'];
        $module = $customPopupSearchList['module'];
        $moduleBean = BeanFactory::newBean($module);

        $popupMeta = [];
        include $customPopupSearchList['file'];
        if (empty($popupMeta)) {
            return;
        }

        $changed = false;
        foreach ($fields as $field => $replacement) {
            $replacementDef = $moduleBean->getFieldDefinition($replacement);
            if (empty($replacementDef)) {
                continue;
            }

            if (!empty($popupMeta['whereClauses'][$field])) {
                unset($popupMeta['whereClauses'][$field]);
                $table = $replacementDef['source'] === 'custom_fields' ?
                    $moduleBean->get_custom_table_name() : $moduleBean->getTableName();
                $popupMeta['whereClauses'][$replacement] = "$table.$replacement";
                $changed = true;
            }

            if (!empty($popupMeta['searchInputs'])) {
                foreach ($popupMeta['searchInputs'] as $idx => $searchField) {
                    if ($searchField === $field) {
                        $popupMeta['searchInputs'][$idx] = $replacement;
                        $changed = true;
                    }
                }
            }

            if (!empty($popupMeta['searchdefs'][$field])) {
                $searchField = $popupMeta['searchdefs'][$field];
                $searchField['name'] = $replacement;
                unset($searchField['label']);
                unset($popupMeta['searchdefs'][$field]);
                $popupMeta['searchdefs'][$replacement] = $searchField;
                $changed = true;
            }

            if (!empty($popupMeta['listviewdefs'][sugarStrToUpper($field)])) {
                $searchField = $popupMeta['listviewdefs'][sugarStrToUpper($field)];
                $searchField['name'] = $replacement;
                unset($searchField['label']);
                unset($popupMeta['listviewdefs'][sugarStrToUpper($field)]);
                $popupMeta['listviewdefs'][sugarStrToUpper($replacement)] = $searchField;
                $changed = true;
            }
        }

        if ($changed) {
            write_array_to_file(
                'popupMeta',
                $popupMeta,
                $customPopupSearchList['file']
            );
        }
    }

    /**
     * Gets information about files using passed in glob strings
     *
     * @param string $globString a glob-compatible string for searching file names in the system
     * @param array $globVariables a mapping of {variable name} => {index of variable in file path separated by "/"}
     *                             to populate in the results
     * @return array information about all files that match the glob string -
     *               each file's path, and its variables defined by globVariables
     */
    protected function getFilesInfo($globString, $globVariables)
    {
        $customMetadata = [];

        // Get active custom metadata
        $customFiles = glob("custom/$globString");
        foreach ($customFiles as $customFile) {
            $customFileParts = explode('/', $customFile);
            $customFileInfo = [
                'file' => $customFile,
            ];
            foreach ($globVariables as $globVariable => $globIndex) {
                if (is_integer($globIndex) && !empty($customFileParts[$globIndex + 1])) {
                    $customFileInfo[$globVariable] = $customFileParts[$globIndex + 1];
                }
            }
            $customMetadata[] = $customFileInfo;
        }

        // Get historical custom metadata
        $customFiles = glob("custom/history/{$globString}_*");
        foreach ($customFiles as $customFile) {
            $customFileParts = explode('/', $customFile);
            $customFileInfo = [
                'file' => $customFile,
            ];
            foreach ($globVariables as $globVariable => $globIndex) {
                if (is_integer($globIndex) && !empty($customFileParts[$globIndex + 2])) {
                    $customFileInfo[$globVariable] = $customFileParts[$globIndex + 2];
                }
            }
            $customMetadata[] = $customFileInfo;
        }

        return $customMetadata;
    }

    /**
     * Copies DB values for existing custom fields into their new stock
     * replacement fields
     *
     * @param string $module the module name
     * @param array $fields a mapping of {name of field to replace} => {replacement field name}
     */
    protected function swapDBValues($module, $fields)
    {
        $seed = BeanFactory::newBean($module);
        $table = $seed->getTableName();
        $customTable = $seed->get_custom_table_name();

        $qb = $this->db->getConnection()->createQueryBuilder();
        $qb->update($table);
        foreach ($fields as $field => $replacement) {
            $subquery = $this->db->getConnection()->createQueryBuilder();
            $subquery->select($field)
                ->from($customTable)
                ->where($subquery->expr()->eq($customTable . '.id_c', $table . '.id'));

            $qb->set($replacement, '(' . $subquery->getSQL() . ')');
        }
        $qb->execute();
    }
}
