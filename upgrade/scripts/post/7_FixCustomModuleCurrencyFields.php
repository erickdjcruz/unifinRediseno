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
 * Adds the convertToBase and showTransactionalAmount flags to currency fields
 * in custom modules.
 *
 * This change does not apply to fields created in studio, only those created
 * in module builder or through custom code. Currency fields created through
 * studio automatically get these changes on upgrade.
 */
class SugarUpgradeFixCustomModuleCurrencyFields extends UpgradeScript
{
    public $order = 7500;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.1', '<')) {
            $this->log('Fixing custom module currency fields');

            $customModules = $this->getCustomModules();
            if (empty($customModules)) {
                $this->log('No custom modules installed, skipping...');
                return;
            }

            foreach ($customModules as $module) {
                $this->fixCurrencyFieldsForModule($module);
            }
        }
    }

    /**
     * Iterate through a module's fields and fix each of the currency fields that
     * require changes.
     * @param $module
     */
    private function fixCurrencyFieldsForModule($module)
    {
        $bean = BeanFactory::newBean($module);
        $fieldDefs = $bean->getFieldDefinitions();

        $fields = [];
        foreach ($fieldDefs as $fieldName => $fieldDef) {
            if ($this->isFieldValidToUpgrade($fieldDef)) {
                $this->fixCurrencyField($bean, $fieldDef);
                $fields[] = $fieldName;
            }
        }

        $this->log('Fixed ' . sizeof($fields) . ' fields for module ' . $module . ': ' . implode(', ', $fields));
    }

    /**
     * Fix an individual currency field
     * @param $bean
     * @param $fieldDef
     */
    private function fixCurrencyField($bean, $fieldDef)
    {
        $field = get_widget('currency');

        $fieldDef['convertToBase'] = true;
        $fieldDef['showTransactionalAmount'] = true;

        $field->populateFromRow($fieldDef);
        $field->module = $bean;

        // If a field in a custom module was created in module builder or through custom code,
        // it uses StandardField.
        $df = new StandardField($bean->module_name);
        $df->setup($bean);

        $beanName = BeanFactory::getObjectName($bean->module_name);
        $df->writeVardefExtension($beanName, $field, $fieldDef);
    }

    /**
     * Checks if a field should be upgraded. If convertToBase or showTransactionalAmount
     * are already set, assume whoever wrote these fields knows what they're doing and
     * leave them alone.
     * @param $fieldDef
     * @return bool
     */
    private function isFieldValidToUpgrade($fieldDef)
    {
        $hasFields = isset($fieldDef['convertToBase']) || isset($fieldDef['showTransactionalAmount']);
        $isStudioField = isset($fieldDef['source']) && $fieldDef['source'] === 'custom_fields';
        return $fieldDef['type'] === 'currency' && !$hasFields && !$isStudioField;
    }

    /**
     * Gets the list of installed custom modules
     * @return array
     */
    private function getCustomModules()
    {
        global $moduleList;

        $customModules = [];
        foreach ($moduleList as $module) {
            if ($this->isCustomModule($module)) {
                $customModules[] = $module;
            }
        }

        return $customModules;
    }

    /**
     * Checks if the provided module is a custom module
     * @param $module
     * @return bool
     */
    private function isCustomModule($module)
    {
        if (strpos($module, 'pmse_') !== false) {
            return false;
        }
        $customFiles = glob('modules/*/*_sugar.php', GLOB_NOSORT);
        foreach ($customFiles as $customFile) {
            $moduleName = str_replace('_sugar', '', pathinfo($customFile, PATHINFO_FILENAME));
            if ($module === $moduleName) {
                return true;
            }
        }
        return false;
    }
}
