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
 * Update date_time_end in schedulers table.
 */
class SugarUpgradeUpdateSchedulersDateTimeEnd extends UpgradeDBScript
{
    public $order = 9999;

    /**
     * Execute upgrade tasks
     * This script updates date_time_end in schedulers table
     * @see UpgradeScript::run()
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.0.0', '<')) {
            $this->log('Updating date_time_end in schedulers table');
            $schedulers = [
                'Process Workflow Tasks',
                'Run Report Generation Scheduled Tasks',
                'Prune Tracker Tables',
                'Check Inbound Mailboxes',
                'Run Nightly Process Bounced Campaign Emails',
                'Run Nightly Mass Email Campaigns',
                'Prune Database on 1st of Month',
                'Update tracker_sessions Table',
                'Run Email Reminder Notifications',
                'Clean Jobs Queue',
                'Create Future TimePeriods',
                'Prune Old Record Lists',
                'Sugar Heartbeat',
                'Remove temporary files',
                'Remove diagnostic tool files',
                'Remove temporary PDF files',
                'SugarBPM™ Scheduled Job',
                'Publish approved articles & Expired KB Articles.',
                'Rebuild Denormalized Team Security Data',
                'Activity Stream Purger',
                'Update product definition',
                'Process Time-Aware Schedules',
                'Run Active Data Archives/Deletions',
                'Elasticsearch Queue Scheduler',
            ];
            $schedulerStr = "'" . implode("', '", $schedulers) . "'";
            $sql = 'UPDATE schedulers SET date_time_end = NULL WHERE name IN (' .
                $schedulerStr .
                ') AND date_time_end IS NOT NULL';
            $this->executeUpdate($sql);
        }
    }
}
