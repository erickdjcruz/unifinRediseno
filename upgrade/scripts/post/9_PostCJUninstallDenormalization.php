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

class SugarUpgradePostCJUninstallDenormalization extends UpgradeScript
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->shouldRun()) {
            $this->updateDenormalizationFieldsForModules();
        }
    }

    /**
     * Check if the upgrade script can be executed
     *
     * @return bool
     * @throws SugarQueryException
     */
    protected function shouldRun()
    {
        $query = new SugarQuery();
        $query->from(\BeanFactory::newBean('UpgradeHistory'), ['add_deleted' => false]);
        $query->where()->starts('id_name', 'addoptify-customer-journey');
        $result = $query->execute();

        return is_countable($result) ? count($result) : 0 > 0 &&
            version_compare($this->from_version, '12.2.0', '>=') &&
            version_compare($this->to_version, '13.1.0', '<=');
    }

    /**
     * Denorm fields that have been removed in CJ Pre install
     * script, would be added here
     */
    protected function updateDenormalizationFieldsForModules()
    {
        $targetVersion = $this->to_version;
        $jobsAdded = [];

        $toBeUpdateDenormFields = [];

        $administration = Administration::getSettings('denormalization');
        $denormalization_field_list = $administration->settings['denormalization_field_list'] ?? [];

        foreach ($denormalization_field_list as $module => $fields) {
            $toBeUpdateDenormFields[$module] = array_keys($fields);
        }

        $adminUser = BeanFactory::newBean('Users')->getSystemUser();
        $this->log('Updating denormalization state');
        foreach ($toBeUpdateDenormFields as $module => $fieldNames) {
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
