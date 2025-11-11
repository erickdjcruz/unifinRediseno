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

class SugarUpgradeFixCustomEscalationFields extends UpgradeScript
{
    public $order = 7500;
    public $type = self::UPGRADE_ALL;

    /**
     * The ViewdefManager
     */
    private $viewdefManager;

    /**
     * The modules that are enabled for Escalations
     */
    private $escalationEnabledModules = [
        'Accounts',
        'Bugs',
        'Cases',
        'Opportunities',
    ];

    public function run()
    {
        if ($this->toFlavor('ent') && version_compare($this->from_version, '11.2.0', '<')) {
            $this->positionIsEscalatedFieldForRecordDashlet();
        }
    }

    /**
     * Re-position is_escalated field to the first field for recorddashlet view
     */
    protected function positionIsEscalatedFieldForRecordDashlet()
    {
        $platform = 'base';
        $view = 'recorddashlet';
        $fieldName = 'is_escalated';

        $this->log("Positioning $fieldName field for modules: " . implode(', ', $this->escalationEnabledModules));

        foreach ($this->escalationEnabledModules as $module) {
            $fileName = "custom/modules/{$module}/clients/{$platform}/views/{$view}/{$view}.php";

            if (!file_exists($fileName)) {
                $this->log("Custom $view view not found at $fileName");
                continue;
            }

            $manager = $this->getViewdefManager();
            $data = $manager->loadViewdef($platform, $module, $view);

            $panelBodyIndex = array_search('panel_body', array_column($data['panels'], 'name'));

            if ($panelBodyIndex === false) {
                $this->log("panel_body not found at $fileName");
                continue;
            }

            $panelBody = $data['panels'][$panelBodyIndex];
            $panelBodyFields = $panelBody['fields'];

            if (empty($panelBodyFields)) {
                $this->log("panel_body fields empty at $fileName");
                continue;
            }

            // assume the is_escalated field was appended to the panel body
            $isEscalatedFieldIndex = array_key_last($panelBodyFields);

            // if the last index was not the is_escalated field, perform
            // an expensive search to find the correct index
            if (!is_array($panelBodyFields[$isEscalatedFieldIndex]) ||
                ($panelBodyFields[$isEscalatedFieldIndex]['name'] !== $fieldName)) {
                $isEscalatedFieldIndex = false;

                foreach ($panelBodyFields as $index => $field) {
                    // is_escalated field is always an array
                    if (empty($field) || !is_array($field)) {
                        continue;
                    }

                    if ($field['name'] === $fieldName) {
                        $isEscalatedFieldIndex = $index;
                        break;
                    }
                }
            }

            if ($isEscalatedFieldIndex === 0) {
                $this->log("is_escalated field is already at the top at $fileName");
                continue;
            }

            // if is_escalated field is not found in metadata, the recorddashlet viewdef
            // is a recent addition and not picked up by Merge7
            $field = [];
            if ($isEscalatedFieldIndex === false) {
                $field[] = $this->getIsEscalatedFieldDef();
            } else {
                $field = array_splice($panelBodyFields, $isEscalatedFieldIndex, 1);
            }

            array_unshift($panelBodyFields, $field[0]);

            $data['panels'][$panelBodyIndex]['fields'] = $panelBodyFields;
            $manager->saveViewdef($data, $module, $platform, $view);
        }
    }

    /**
     * Get the ViewdefManager and cache it
     *
     * @return ViewdefManager
     */
    protected function getViewdefManager(): ViewdefManager
    {
        if (!$this->viewdefManager) {
            $this->viewdefManager = new ViewdefManager();
        }

        return $this->viewdefManager;
    }

    /**
     * Return the is_escalated field def
     *
     * @return array
     */
    protected function getIsEscalatedFieldDef(): array
    {
        return
            [
                'name' => 'is_escalated',
                'type' => 'badge',
                'badge_label' => 'LBL_ESCALATED',
                'warning_level' => 'important',
                'dismiss_label' => true,
                'span' => 12,
            ];
    }
}
