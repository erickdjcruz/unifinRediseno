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
 * Class to remove 2 stock reports
 */
class SugarUpgradeRemoveReports extends UpgradeScript
{
    public $order = 9100;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '10.0.0', '<')) {
            $this->removeReports();
        }
    }

    /**
     *
     * Remove 2 stock reports if not been modified
     */
    public function removeReports()
    {
        $ids = ['79c210e4-073b-11ea-a8af-acde48001122', '6681c340-071c-11ea-acfb-acde48001122'];
        $sql = "DELETE FROM saved_reports WHERE id IN ('" . implode("', '", $ids) . "')";
        $sql .= ' AND date_entered = date_modified';
        $this->db->query($sql);
    }
}
