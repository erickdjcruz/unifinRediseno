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

use Sugarcrm\Sugarcrm\MetaData\ViewdefManager;

/**
 * Fix the fields used in the console multi-line-lists. Adds field types
 * and related_fields where required.
 */
class SugarUpgradeFixConsoleFields extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '10.3.0', '<') && $this->toFlavor('ent')) {
            $moduleNames = ['Accounts', 'Opportunities', 'Cases'];
            foreach ($moduleNames as $moduleName) {
                if ($this->shouldUpdateForModule($moduleName)) {
                    $this->log("Updating console multi-line-list fields for $moduleName");
                    $this->updateForModule($moduleName);
                }
            }
        }
    }

    /**
     * Determine if the multi-line-list for the module needs to be updated
     * @param $moduleName string
     * @return bool
     */
    private function shouldUpdateForModule($moduleName)
    {
        return file_exists('custom/modules/' . $moduleName . '/clients/base/views/multi-line-list/multi-line-list.php');
    }

    /**
     * Updates the fields for the given module
     * @param $moduleName string
     */
    private function updateForModule($moduleName)
    {
        $metadataManager = MetaDataManager::getManager('base');
        $viewdefManager = new ViewdefManager();

        $fields = $metadataManager->getVarDef($moduleName)['fields'];
        $multiLineList = $viewdefManager->loadViewdef('base', $moduleName, 'multi-line-list');

        foreach ($multiLineList['panels'] as $panelIdx => $panel) {
            foreach ($panel['fields'] as $fieldIdx => $field) {
                foreach ($field['subfields'] as $subfieldIdx => $subfield) {
                    if (!empty($subfield['widget_name'])) {
                        continue;
                    }
                    $name = $subfield['name'];
                    if (empty($subfield['type'])) {
                        $subfield['type'] = $fields[$name]['type'];
                    }
                    if (!empty($fields[$name]['related_fields'])) {
                        $subfield['related_fields'] = $fields[$name]['related_fields'];
                    }
                    $multiLineList['panels'][$panelIdx]['fields'][$fieldIdx]['subfields'][$subfieldIdx] = $subfield;
                }
            }
        }
        $viewdefManager->saveViewdef($multiLineList, $moduleName, 'base', 'multi-line-list');
    }
}
