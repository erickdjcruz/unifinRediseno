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

class SugarUpgradeVisualPipelineAddNewConfigs extends UpgradeScript
{
    public $order = 9131;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if ($this->shouldUpdatePipelineDefaults()) {
            VisualPipelineDefaults::addDefaults($this->getNew131Defaults());
        }
    }

    public function shouldUpdatePipelineDefaults()
    {
        return version_compare($this->from_version, '13.1.0', '<');
    }

    /**
     * Returns new values for Tile View to use post 13.1
     *
     * @return array updated config settings for Tile View to use post 13.1
     */
    public function getNew131Defaults()
    {
        return [
            'show_column_count' => [
                'Cases' => true,
                'Opportunities' => true,
                'Tasks' => true,
                'Leads' => true,
            ],
            'show_column_total' => [
                'Cases' => false,
                'Opportunities' => true,
                'Tasks' => false,
                'Leads' => false,
            ],
            'total_field' => [
                'Cases' => '',
                'Opportunities' => 'amount',
                'Tasks' => '',
                'Leads' => '',
            ],
        ];
    }
}
