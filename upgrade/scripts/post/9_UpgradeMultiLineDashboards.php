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
 * Convert muilt-line dashboards to focus dashboards.
 */
class SugarUpgradeUpgradeMultiLineDashboards extends UpgradeScript
{
    public $order = 9901;
    public $type = self::UPGRADE_DB;

    /**
     *
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        if (version_compare($this->from_version, '13.1.0', '>=')) {
            // do nothing if upgrading from 13.1.0 or newer
            return;
        }
        $this->log('Upgrading muilti-line dashboards');
        // change view_name
        $focusView = $this->db->quoted('focus');
        $multiLineView = $this->db->quoted('multi-line');
        $sql = "UPDATE dashboards SET view_name=$focusView WHERE view_name=$multiLineView";
        $this->db->query($sql);
        $this->log('Multi-line dashboards have been converted to focus dashboards');
    }
}
