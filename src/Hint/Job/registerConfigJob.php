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

namespace Sugarcrm\Sugarcrm\Hint\Job;

use Sugarcrm\Sugarcrm\Hint\Config\ConfigTrait;
use Sugarcrm\Sugarcrm\Hint\HintConstants;
use Sugarcrm\Sugarcrm\Hint\ConfigurationManager;

class registerConfigJob implements \RunnableSchedulerJob
{
    use ConfigTrait;

    public const NAME = 'Hint Register Config Job';
    //  Postpones the Job if bean not found by 60 secs.
    public const JOB_POSTPONE_TIMEOUT_SECS = 1 * 60;

    protected $job;

    /**
     * {@inheritdoc}
     */
    public function setJob(\SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * {@inheritdoc}
     */
    public function run($data)
    {
        if (!hasSystemHintLicense()) {
            return $this->job->succeedJob(translate('LBL_HINT_NO_LICENSE_ACCESS'));
        }

        // no scheduler means this job finished successfully some time ago
        if (empty($this->job->scheduler_id)) {
            return $this->job->succeedJob('registerConfigJob is initialized');
        }

        $hintApiCalls = new \HintApi();
        $scheduler = \BeanFactory::retrieveBean('Schedulers', $this->job->scheduler_id);

        if (!$scheduler || $scheduler->status !== 'Active') {
            return $this->job->succeedJob('No active scheduler');
        }

        // There are times after an installation that our custom Hint classes are not
        // quite fully setup when this job runs. If this is the case, we simply postpone
        // the job to run later and hopefully give Mango the needed to finish bean
        // instantiation.
        if (!class_exists('HintEnrichFieldConfig')) {
            return $this->postpone('Postponing config registration: hint tables not yet ready');
        }

        $isAlreadySynced = \HintEnrichFieldConfig::getSyncHintConfigurationStatus();
        if ($isAlreadySynced) {
            $scheduler->status = 'Inactive';
            $scheduler->save();
            return $this->job->succeedJob('registerConfigJob is initialized');
        } else {
            // configMetaData to be sent to v2 endpoint
            $configDataBeanData = [
                'Leads' => [
                    'fields' => ConfigurationManager::createInitialModuleConfig(HintConstants::DEFAULT_LEADS_FIELDS),
                ],
                'Accounts' => [
                    'fields' => ConfigurationManager::createInitialModuleConfig(HintConstants::DEFAULT_ACCOUNTS_FIELDS),
                ],
                'Contacts' => [
                    'fields' => ConfigurationManager::createInitialModuleConfig(HintConstants::DEFAULT_CONTACTS_FIELDS),
                ],
            ];

            $configDataBean = \BeanFactory::retrieveBean('HintEnrichFieldConfigs');
            $alreadyPostedBean = \HintEnrichFieldConfig::getHintEnrichFieldConfigBean();
            $identitySuccessResponse = $hintApiCalls->registerInstanceToCompanyIdentityEndpoint();
            $hintApiCalls->createToken('', '');

            // the 1st run
            if (empty($configDataBean)) {
                return $this->postpone('Postponing, no subscription');
            }

            $configDataBeanDataConverted = json_encode($configDataBeanData, JSON_HEX_TAG);

            // prevents multiple run and runs only if configMetadata is not found in DB.
            if ($configDataBean && empty($alreadyPostedBean)) {
                \HintEnrichFieldConfig::createHintEnrichFieldConfig($configDataBeanDataConverted);
            }

            $configSuccessResponse = $hintApiCalls->registerConfigToEnrichBeanEndpoint($hintApiCalls->privilegeToken, $configDataBeanData);

            // On successful registration of config_data and created the Identity the scheduler is no longer needed.
            // Until then we have the scheduler 'Active'.
            if ($configSuccessResponse['status'] === 201 && $identitySuccessResponse['status'] === 200) {
                \HintEnrichFieldConfig::syncHintConfigEndpointStatus();
                $scheduler->status = 'Inactive';
                $scheduler->save();
            }

            // REMIND: To Add POST method once v2 and /config-enrich-bean endpoints are completely ready.
            return $this->job->succeedJob('registerConfigJob is initialized');
        }
    }

    /**
     * Postpones current job
     *
     * @param string $message
     * @param int $timeout
     * @return bool
     */
    private function postpone($message = '', $timeout = self::JOB_POSTPONE_TIMEOUT_SECS)
    {
        // to avoid infinite message concatenation
        $this->job->message = '';

        return $this->job->postponeJob($message, $timeout);
    }
}
