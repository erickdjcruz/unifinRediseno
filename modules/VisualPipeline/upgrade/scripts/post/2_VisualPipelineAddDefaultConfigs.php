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

class SugarUpgradeVisualPipelineAddDefaultConfigs extends UpgradeScript
{
    public $order = 2100;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        $admin = BeanFactory::newBean('Administration');
        $adminConfig = $admin->getConfigForModule('VisualPipeline');
        if ($this->shouldInstallPipelineDefaults()) {
            VisualPipelineDefaults::setupPipelineSettings();
        } elseif ($this->shouldUpdatePipelineDefaults()) {
            $new93Config = $this->getNew93Defaults();
            VisualPipelineDefaults::addDefaults($new93Config);
        }
    }

    public function shouldInstallPipelineDefaults()
    {
        $isConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelowOrAt91Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.1.0', '<=');
        return $isConversion || $isBelowOrAt91Ent;
    }

    public function shouldUpdatePipelineDefaults()
    {
        $isConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelowOrAt93Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.3.0', '<=');
        return $isConversion || $isBelowOrAt93Ent;
    }

    /**
     * Returns the default values for Tile View to use post 9.3 along with the availableColumn values
     *
     * @param array $adminConfig pass any existing settings/defaults for the tile view
     * @return array updated config settings for Tile View to use post 9.3
     */
    public function getNew93Defaults()
    {
        return [
            'enabled_modules' => 'Leads',
            'table_header' => ['Leads' => 'status',],

            'hidden_values' => ['Leads' => [],],
            'tile_header' => ['Leads' => 'full_name',],
            'tile_body_fields' => [
                'Leads' => [
                    'email',
                    'account_name',
                    'phone_work',
                ],
            ],
            'records_per_column' => ['Leads' => '10',],
            'available_columns' => [
                'Cases' => [
                    'status' => [
                        'New' => 'New',
                        'Assigned' => 'Assigned',
                        'Closed' => 'Closed',
                        'Pending Input' => 'Pending Input',
                        'Rejected' => 'Rejected',
                        'Duplicate' => 'Duplicate',
                    ],
                ],
                'Opportunities' => [
                    'sales_stage' => [
                        'Prospecting' => 'Prospecting',
                        'Qualification' => 'Qualification',
                        'Needs Analysis' => 'Needs Analysis',
                        'Value Proposition' => 'Value Proposition',
                        'Id. Decision Makers' => 'Id. Decision Makers',
                        'Perception Analysis' => 'Perception Analysis',
                        'Proposal/Price Quote' => 'Proposal/Price Quote',
                        'Negotiation/Review' => 'Negotiation/Review',
                    ],
                ],
                'Tasks' => [
                    'status' => [
                        'Not Started' => 'Not Started',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                        'Pending Input' => 'Pending Input',
                        'Deferred' => 'Deferred',
                    ],
                ],
                'Leads' => [
                    'status' => [
                        'New' => 'New',
                        'Assigned' => 'Assigned',
                        'In Process' => 'In Process',
                        'Converted' => 'Converted',
                        'Recycled' => 'Recycled',
                        'Dead' => 'Dead',
                    ],
                ],
            ],
        ];
    }
}
