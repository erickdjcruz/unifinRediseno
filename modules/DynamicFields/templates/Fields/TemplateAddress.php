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

class TemplateAddress extends TemplateField
{
    // To keep consistency with OOTB address groups, add Street to the fields
    private $addressFieldNames = ['Street', 'City', 'State', 'PostalCode', 'Country'];

    /**
     * @param DynamicField $dynamicField
     * @return array
     */
    public function getContainedDefs(DynamicField $dynamicField): array
    {
        $this->group = $dynamicField->getDBName($this->name);

        $fieldDefs = [];
        $fields = $this->createFields();
        foreach ($fields as $field) {
            $fieldDefs[] = $field->get_field_def();
        }
        return $fieldDefs;
    }

    /**
     * @return TemplateField[]
     */
    private function createFields(): array
    {
        $addressFields = [];
        // Create the main label to be used as the group label
        $this->group_label = 'LBL_' . strtoupper($this->name);

        foreach ($this->addressFieldNames as $addressFieldName) {
            $systemLabel = $this->group_label . '_' . strtoupper($addressFieldName);

            if ($addressFieldName === 'Street') {
                $addressField = new TemplateAddressStreet();
                $addressField->len = 150;
                $addressField->ext2 = 2;
                $addressField->ext3 = 20;
            } else {
                $addressField = new TemplateField();
                $addressField->len = ($addressFieldName === 'PostalCode') ? 20 : 100;
            }
            $addressField->name = $this->name . '_' . strtolower($addressFieldName);
            $addressField->label = $addressField->vname = $systemLabel;
            // Bug 58560 - Add the group to this field so it gets written to the custom vardefs
            $addressField->group = $this->group;
            $addressField->group_label = $this->group_label;

            // Maintain unified search setting for 'Street'
            $addressField->supports_unified_search = $addressField == 'Street';

            // audit and pii should be in sync whith this address
            $addressField->audited = $this->audited;
            $addressField->pii = $this->pii;

            $addressFields[$addressFieldName] = $addressField;
        }
        return $addressFields;
    }

    public function save($dynamicField)
    {
        // Bug 58560 - Set the group name since addresses are part of a group
        $this->group = $dynamicField->getDBName($this->name);

        $parser = new ParserLabel($dynamicField->getModuleName(), $dynamicField->getPackageName());

        // Clean up the labels so they more accurately reflect the actual field
        if (!empty($this->label_value)) {
            $labelValue = $this->label_value;
        } else {
            $labelValue = empty($_REQUEST['labelValue']) ? '' : $_REQUEST['labelValue'];
        }

        // Create the main label to be used as the group label
        $this->group_label = 'LBL_' . strtoupper($this->name);

        // If there is a label to use, space it here for use below
        if (!empty($labelValue)) {
            $labelValue .= ' ';
        }
        // To prevent mutilple calls to the metadata api cache rebuilder, queue
        MetaDataManager::enableCacheRefreshQueue();

        // Save the main group label to the language extension now
        $parser->handleSave(['label_' . $this->group_label => $this->group_label], $GLOBALS['current_language']);

        $fields = $this->createFields();
        foreach ($fields as $addressFieldName => $addressField) {
            // Use the entered label value as a prefix instead of the field name
            $parser->handleSave(['label_' . $addressField->label => $labelValue . $addressFieldName], $GLOBALS['current_language']);
            $addressField->save($dynamicField);
        }

        // Handle the metadata api update now
        MetaDataManager::runCacheRefreshQueue();
    }
}
