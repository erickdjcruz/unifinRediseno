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

use Sugarcrm\Sugarcrm\Denormalization\Relate\FieldConfig;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;

/**
 * Make relate fields account_name denormalized for Contacts, Opportunities, RLI, Cases
 */
class SugarUpgradeUpdateDenormalizationStateForAccountName extends UpgradeScript
{
    public $order = 9999;

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        $targetVersion = '11.1.0';
        $targetVersionPurchasedLineItems = '12.2.0';
        $targetVersionQuotes = '12.2.0';
        $modules = [];
        $jobsAdded = [];
        if (version_compare($this->from_version, $targetVersion, '<')) {
            $modules = [
                'Contacts' => [
                    'account_name',
                ],
                'Opportunities' => [
                    'account_name',
                ],
                'RevenueLineItems' => [
                    'account_name',
                ],
                'Cases' => [
                    'account_name',
                ],
            ];
        }
        if (version_compare($this->from_version, $targetVersionPurchasedLineItems, '<')) {
            $modules['PurchasedLineItems'] = [
                'account_name',
            ];
        }
        if (version_compare($this->from_version, $targetVersionQuotes, '<')) {
            $modules['Quotes'] = [
                'billing_account_name',
            ];
        }


        $adminUser = BeanFactory::newBean('Users')->getSystemUser();
        $this->log('Updating denormalization state for Contacts, Opportunities, RLI, Cases');
        foreach ($modules as $module => $fieldNames) {
            foreach ($fieldNames as $fieldName) {
                $bean = BeanFactory::newBean($module);
                $def = $bean->getFieldDefinition($fieldName);
                if (!empty($def['is_denormalized'])) {
                    continue;
                }
                $entity = new Entity($bean, $fieldName);

                $config = new FieldConfig();
                $config->markFieldAsDenormalized($entity, true);

                $options = [
                    'module_name' => $entity->getTargetModuleName(),
                    'field_name' => $entity->fieldName,
                    'tmp_table_name' => 'denorm_tmp_' . $module,
                ];

                /* @var $job SchedulersJob */
                $job = BeanFactory::newBean('SchedulersJobs');
                $job->name = 'Upgrade_Denormalization_' . $module . '_' . $fieldName;
                $job->target = 'class::' . SugarJobFieldDenormalization::class;
                $job->data = json_encode($options);
                $job->retry_count = 0;
                $job->job_group = 'upgrade_to_' . $targetVersion;
                $job->assigned_user_id = $adminUser->id;

                $queue = new SugarJobQueue();
                $queue->submitJob($job);
                // mark as deleted to disable execution from cron.php. It will be enabled by the Watcher Job added below
                $job->deleted = 1;
                $job->save();
                $jobsAdded[$job->id] = false;
            }
        }

        if (!empty($jobsAdded)) {
            /* @var $job SchedulersJob */
            $job = BeanFactory::newBean('SchedulersJobs');
            $job->name = 'Upgrade_Denormalization_Watcher';
            $job->target = 'function::upgradeDenormalizationStateForSugar11';
            $job->data = json_encode($jobsAdded);
            $job->retry_count = 0;
            $job->job_group = 'upgrade_to_' . $targetVersion;
            $job->assigned_user_id = $adminUser->id;

            $queue = new SugarJobQueue();
            $queue->submitJob($job);
        }
    }
}
