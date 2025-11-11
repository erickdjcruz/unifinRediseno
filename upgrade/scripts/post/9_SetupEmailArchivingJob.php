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
 * Setup SugarJobEmailArchiving
 */
class SugarUpgradeSetupEmailArchivingJob extends UpgradeScript
{
    public $order = 9999;

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        $targetVersion = '13.3.0';
        if (version_compare($this->from_version, $targetVersion, '>=')
            || !BeanFactory::newBean('Emails')->supportsGzip()
            || !isMts() //explicitly checking
        ) {
            return;
        }

        $CTE = <<<CTE
CREATE TEMPORARY TABLE emulate_cte_temp
    (id char(36) NOT NULL,
    INDEX (id))
    DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
CTE;
        DBManagerFactory::getInstance()->query($CTE);
        $insert = <<<INCTE
INSERT INTO emulate_cte_temp (
    SELECT id FROM emails 
    WHERE date_modified <= DATE_SUB(NOW(), INTERVAL 30 DAY)
    LIMIT 100
)
INCTE;
        DBManagerFactory::getInstance()->query($insert);
        // If the oldest record was archived already we decide the job is not necessary
        $sql = <<<EOF
SELECT email_id, description_html
FROM emails_text
WHERE email_id IN (SELECT id FROM emulate_cte_temp)
EOF;
        $latestEmails = DBManagerFactory::getInstance()->getConnection()->fetchAllNumeric($sql);
        /** @var Email $emailBean */
        $emailBean = BeanFactory::newBean('Emails');
        if (is_array($latestEmails)) {
            foreach ($latestEmails as [$emailId, $emailDescription]) {
                if ($emailDescription === null) {
                    continue;
                }
                if ($emailBean->tryUngzipContent($emailDescription) !== null) {
                    return;
                }
            }
        }

        $adminUser = BeanFactory::newBean('Users')->getSystemUser();

        /* @var $job SchedulersJob */
        $job = BeanFactory::newBean('SchedulersJobs');
        $job->name = 'Upgrade_Email_Archiving_Job';
        $job->target = 'class::SugarJobEmailArchiving';
        $job->retry_count = 0;
        $job->job_group = 'upgrade_to_' . $targetVersion;
        $job->assigned_user_id = $adminUser->id;

        $queue = new SugarJobQueue();
        $queue->submitJob($job);
    }
}
