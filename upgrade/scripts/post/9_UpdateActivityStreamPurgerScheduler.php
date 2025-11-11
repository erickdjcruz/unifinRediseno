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
 * Update Activity Stream Purger scheduler to start the job once a week at 4:00 a.m. on Sunday
 */
class SugarUpgradeUpdateActivityStreamPurgerScheduler extends UpgradeScript
{
    public $order = 9999;

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        $targetVersion = '12.0.0';
        if (version_compare($this->from_version, $targetVersion, '<')) {
            $this->log('Updating Activity Stream Purger scheduler to start the job once a week at 4:00 a.m. on Sunday');

            /** @var Scheduler $bean */
            $bean = BeanFactory::newBean('Schedulers');
            $query = new SugarQuery();
            $query->select(['id']);
            $query->from($bean);
            $query->where()->equals('job', 'class::SugarJobActivityStreamPurger');
            $id = $query->getOne();

            if ($id) {
                $bean->retrieve($id);
                $bean->job_interval = '0::4::*::*::0';
                $bean->save();
            }
        }
    }
}
