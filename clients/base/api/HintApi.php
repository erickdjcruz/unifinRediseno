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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sugarcrm\Sugarcrm\Hint\Config\ConfigTrait;
use Sugarcrm\Sugarcrm\Hint\ConfigurationManager;
use Sugarcrm\Sugarcrm\Hint\HintConstants;
use Sugarcrm\Sugarcrm\Hint\Initializer;
use Sugarcrm\Sugarcrm\Hint\Iss\Commands;
use Sugarcrm\Sugarcrm\Hint\Iss\Manager as IssManager;
use Sugarcrm\Sugarcrm\Hint\Logger\Logger;
use Sugarcrm\Sugarcrm\Hint\Manager;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceDisableNotificationsEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceEnableNotificationsEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UserUnlicensedEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\ProcessorFactory;
use Sugarcrm\Sugarcrm\Hint\Queue\QueueProcessor;
use Sugarcrm\Sugarcrm\Hint\Queue\QueueTrait;

class HintApi extends \SugarApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use ConfigTrait;
    use QueueTrait;

    public $privilegeToken = null;
    public $enrichFieldConfig;
    public $issManager;
    public $processorFactory;

    /**
     * HintApi constructor
     */
    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->setLogger(new Logger());
        if (class_exists('HintEnrichFieldConfig')) {
            $this->enrichFieldConfig = new HintEnrichFieldConfig();
        }
        $hintManager = Manager::instance();
        $this->issManager = new IssManager($hintManager->issServiceUrl);
        $this->processorFactory = new ProcessorFactory();
        $this->eventQueue = $this->getEventQueue();
    }

    /**
     * {@inheritdoc}
     */
    public function registerApiRest()
    {
        return [
            'readConfig' => [
                'reqType' => 'GET',
                'path' => ['hint', 'config'],
                'pathVars' => [''],
                'method' => 'readConfig',
                'shortHelp' => 'Reads Hint configuration',
                'longHelp' => 'include/api/help/hint_config_get_help.html',
                'minVersion' => '11.16',
            ],

            'createConfig' => [
                'reqType' => 'POST',
                'path' => ['hint', 'config'],
                'pathVars' => [''],
                'method' => 'updateConfig',
                'shortHelp' => 'Updates Hint configuration',
                'longHelp' => 'include/api/help/hint_config_post_help.html',
                'minVersion' => '11.16',
            ],

            'updateConfig' => [
                'reqType' => 'PUT',
                'path' => ['hint', 'config'],
                'pathVars' => [''],
                'method' => 'updateConfig',
                'shortHelp' => 'Updates Hint configuration',
                'longHelp' => 'include/api/help/hint_config_put_help.html',
                'minVersion' => '11.16',
            ],

            'readEnrichFieldConfig' => [
                'reqType' => 'GET',
                'path' => ['hint', 'enrich', 'config'],
                'pathVars' => [''],
                'method' => 'readEnrichFieldConfig',
                'shortHelp' => 'Reads Hint configuration',
                'longHelp' => 'include/api/help/hint_enrich_config_get_help.html',
                'minVersion' => '11.16',
            ],

            'createEnrichFieldConfig' => [
                'reqType' => 'POST',
                'path' => ['hint', 'enrich', 'config'],
                'pathVars' => [''],
                'method' => 'createEnrichFieldConfig',
                'shortHelp' => 'Posts Hint configuration',
                'longHelp' => 'include/api/help/hint_enrich_config_post_help.html',
                'minVersion' => '11.16',
            ],

            'updateEnrichFieldConfig' => [
                'reqType' => 'PUT',
                'path' => ['hint', 'enrich', 'config'],
                'pathVars' => [''],
                'method' => 'updateEnrichFieldConfig',
                'shortHelp' => 'Updates Hint configuration',
                'longHelp' => 'include/api/help/hint_enrich_config_put_help.html',
                'minVersion' => '11.16',
            ],

            'resync' => [
                'reqType' => 'POST',
                'path' => ['hint', 'insights', 'resync'],
                'pathVars' => [''],
                'method' => 'resync',
                'shortHelp' => 'Triggers instance synchronization',
                'longHelp' => 'include/api/help/hint_insights_resync_post_help.html',
                'minVersion' => '11.16',
            ],

            'token' => [
                'reqType' => 'POST',
                'path' => ['stage2', 'token'],
                'pathVars' => [''],
                'method' => 'createToken',
                'shortHelp' => 'Generates a new Stage2 access token for the user',
                'longHelp' => 'include/api/help/hint_stage2_token_post_help.html',
                'minVersion' => '11.16',
            ],

            'params' => [
                'reqType' => 'GET',
                'path' => ['stage2', 'params'],
                'pathVars' => [''],
                'method' => 'getParams',
                'shortHelp' => 'Returns different Stage2 information particular for the user',
                'longHelp' => 'include/api/help/hint_stage2_params_get_help.html',
                'minVersion' => '11.16',
            ],

            'notificationsServiceToken' => [
                'reqType' => 'POST',
                'path' => ['stage2', 'notificationsServiceToken'],
                'pathVars' => [''],
                'method' => 'createNotificationsServiceToken',
                'shortHelp' => 'Generates a new notifications service access token for the user',
                'longHelp' => 'include/api/help/hint_stage2_notificationsServiceToken_post_help.html',
                'minVersion' => '11.16',
            ],

            'updateConfigNotificationsHint' => [
                'reqType' => 'PUT',
                'path' => ['hint', 'config', 'notifications'],
                'pathVars' => [''],
                'method' => 'updateConfigNotificationsHint',
                'shortHelp' => 'Enables/Disables notifications in the Hint MLP',
                'longHelp' => 'include/api/help/hint_config_notifications_put_help.html',
                'minVersion' => '11.16',
            ],

            'getConfigNotificationsHint' => [
                'reqType' => 'GET',
                'path' => ['get', 'hint', 'configNotificationObject'],
                'pathVars' => [''],
                'method' => 'getConfigNotificationsHint',
                'shortHelp' => 'Retrieves the current notifications enabled/disabled value to display in the Hint Configuration panel',
                'longHelp' => 'include/api/help/hint_get_hint_configNotificationObject_get_help.html',
                'minVersion' => '11.16',
            ],

            'getHintLicenseType' => [
                'reqType' => 'GET',
                'path' => ['hint', 'license', 'check'],
                'pathVars' => [''],
                'method' => 'getHintLicenseType',
                'shortHelp' => 'Checks if the user has Hint license enabled',
                'longHelp' => 'include/api/help/hint_license_check_get_help.html',
                'minVersion' => '11.16',
            ],

            'updateDataEnrichConfigFields' => [
                'reqType' => 'POST',
                'path' => ['hint', 'data', 'enrich', 'fields'],
                'pathVars' => [''],
                'method' => 'updateDataEnrichmentFieldsConfig',
                'shortHelp' => 'Updates Data Enrichment Hint configuration',
                'longHelp' => 'include/api/help/hint_data_enrich_fields_post_help.html',
                'minVersion' => '11.16',
            ],
        ];
    }

    /**
     * Read config
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     * @throws \SugarApiExceptionNotAuthorized
     * @throws \SugarApiExceptionError
     */
    public function readConfig(\ServiceBase $api, array $args): array
    {
        ConfigurationManager::ensureAdminUser();

        try {
            $loggerConfig = $this->config->getLoggerConfig();
        } catch (\Throwable $e) {
            throw new \SugarApiExceptionError($e->getMessage());
        }

        return [
            'logger' => $loggerConfig,
        ];
    }

    /**
     * Get Hint license type
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     * @throws \SugarApiExceptionError
     */
    public function getHintLicenseType(\ServiceBase $api, array $args): array
    {
        try {
            $hintLicenseCheck = ConfigurationManager::isHintUser();
            return [
                'isHintUser' => $hintLicenseCheck,
            ];
        } catch (\Throwable $e) {
            throw new \SugarApiExceptionError($e->getMessage());
        }
    }

    /**
     * Update config
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     * @throws \SugarApiExceptionInvalidParameter
     * @throws \SugarApiExceptionNotAuthorized
     */
    public function updateConfig(\ServiceBase $api, array $args): array
    {
        ConfigurationManager::ensureAdminUser();

        try {
            $loggerConfig = $this->config->setLoggerConfig($args['logger'] ?? []);
        } catch (\Throwable $e) {
            throw new \SugarApiExceptionInvalidParameter($e->getMessage());
        }

        return [
            'logger' => $loggerConfig,
        ];
    }

    /**
     * Read enrich field config
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     */
    public function readEnrichFieldConfig(\ServiceBase $api, array $args): array
    {
        try {
            $logger = new Logger();
            if (class_exists('HintEnrichFieldConfig')) {
                $response = $this->enrichFieldConfig->getHintEnrichFieldConfigBean($args);
                return [
                    'response' => $response,
                ];
            } else {
                $logger->error('Class Not found Exception: HintEnrichFieldConfig');
                throw new \Exception('Cannot find Class: HintEnrichFieldConfig');
            }
        } catch (\Throwable $e) {
            throw new \SugarApiExceptionInvalidParameter($e->getMessage());
        }
    }

    /**
     * Create enrich field config
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     */
    public function createEnrichFieldConfig(\ServiceBase $api, array $args): array
    {
        ConfigurationManager::ensureAdminUser();
        try {
            $logger = new Logger();
            if (class_exists('HintEnrichFieldConfig')) {
                $response = $this->enrichFieldConfig->createHintEnrichFieldConfig($args);
                return [
                    'response' => $response,
                ];
            } else {
                $logger->error('Class Not found Exception: HintEnrichFieldConfig');
                throw new \Exception('Cannot find Class: HintEnrichFieldConfig');
            }
        } catch (\Throwable $e) {
            throw new \SugarApiExceptionInvalidParameter($e->getMessage());
        }
    }

    /**
     * Update enrich field config
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateEnrichFieldConfig(\ServiceBase $api, array $args): array
    {
        ConfigurationManager::ensureAdminUser();
        try {
            $logger = new Logger();
            if (class_exists('HintEnrichFieldConfig')) {
                $response = $this->enrichFieldConfig->updateHintEnrichFieldConfigBeans($args);
                return [
                    'response' => $response,
                ];
            } else {
                $logger->error('Class Not found Exception: HintEnrichFieldConfig');
                throw new \Exception('Cannot find Class: HintEnrichFieldConfig');
            }
        } catch (\Throwable $e) {
            throw new \SugarApiExceptionInvalidParameter($e->getMessage());
        }
    }

    /**
     * Resync
     *
     * @param \ServiceBase $api
     * @param array $args
     * @return array
     * @throws \SugarApiExceptionNotAuthorized
     * @throws \SugarApiException
     */
    public function resync(ServiceBase $api, array $args): array
    {
        ConfigurationManager::ensureAdminUser();

        try {
            $this->getInitializer()->resync();
        } catch (\Throwable $e) {
            $errorMessage = sprintf('Resync failed: %s', $e->getMessage());
            $GLOBALS['log']->fatal($errorMessage);
            throw new \SugarApiException($errorMessage);
        }

        return [
            'success' => true,
        ];
    }

    /**
     * Create token. Called from multiple places
     *
     * @param mixed $api
     * @param mixed $args
     * @return array
     */
    public function createToken($api, $args)
    {
        $tokenResponse = Manager::instance()->getNewAccessToken();
        $this->privilegeToken = $tokenResponse['privilegeToken'];
        // Only the accessToken is sent to the client.
        $clientResponse = ['accessToken' => $tokenResponse['accessToken']];
        // older servers won't send the subscription type, so be check before using
        if (isset($tokenResponse['subscriptionType'])) {
            $clientResponse['subscriptionType'] = $tokenResponse['subscriptionType'];
        }
        return $clientResponse;
    }

    /**
     * Get params
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getParams(ServiceBase $api, array $args)
    {
        $manager = Manager::instance();
        return [
            'serviceUrl' => $manager->serviceUrl,
            'pushNotificationKey' => $manager->getVAPIDPublicKey(),
            'instanceId' => $manager->instanceId,
            'sugarVersion' => $manager->sugarVersion,
            'isps' => $manager->getISPs(),
            'analyticsUserId' => $manager->getCurrentUserAnalyticsId(),
            'enrichmentServiceUrl' => $manager->serviceUrl . '/hint/data-enrichment/v1',
            'enrichmentServiceUrlV2' => $manager->serviceUrl . '/hint/data-enrichment/v2',
            'notificationsServiceUrl' =>
                $manager->notificationsServiceUrl . '/hint/notifications-service/v1',
        ];
    }

    /**
     * Update data enrichment fields config
     *
     * @param ServiceBase $api
     * @param array $configToUpdate
     * @return array
     */
    public function updateDataEnrichmentFieldsConfig(ServiceBase $api, array $configToUpdate): array
    {
        ConfigurationManager::ensureAdminUser();
        $manager = Manager::instance();
        return $manager->updateDataEnrichmentConfigBean($configToUpdate);
    }

    /**
     * Get config notifications hint
     *
     * Retrieve the notifications enabled/disabled status for the Hint config panel
     *
     * @param ServiceBase $api
     * @param array $args
     * @return string
     */
    public function getConfigNotificationsHint($api, $args)
    {
        $response = ConfigurationManager::getHintConfigEntry(HintConstants::HINT_CONFIG_NOTIFICATION);
        return $response['value'];
    }

    /**
     * Update config notifications hint
     *
     * Update the notifications enabled/disabled status from whatever was set in the
     * UI (Hint config panel)
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateConfigNotificationsHint(ServiceBase $api, array $args): array
    {
        $disableNotifications = $args['disableNotifications'];
        $storedValue = $this->getConfigNotificationsHint(null, null);

        // initially, the notifications status is unset/null. Only continue enabling/disabling
        // when the user explicitly has chosen to explicitly disable notifications, or, is enabling
        // notifications after they were previously explicitly disabled.
        if (($storedValue == null && !$disableNotifications) || ($storedValue == $disableNotifications)) {
            return ['status' => '200'];
        }

        // TODO: notification problems we currently are experiencing may have POSSIBLY
        // been coming from here (more testing needs to be done with a new hint dev package):
        //
        // First of all, the above code that checks the passed disabledNotifications arg
        // against what is actually currently stored in the DB should avoid all unnecessary
        // resyncs that were occurring by simply visiting the hint config admin panel.
        //
        // Secondly, the code below here now simply handles explicit disabling/enabling of
        // notifications. Previously (before the unset/null check at the beginning of this method),
        // the null case was incorrectly being handled here as we were never even grabbing the actual
        // stored notifications status (thus never checking that initial unset/null case).
        if ($disableNotifications) {
            $event = new InstanceDisableNotificationsEvent(['explicitDisable' => $disableNotifications]);
            $this->eventQueue->recordEvent($event);
        } else {
            $event = new InstanceEnableNotificationsEvent();
            $this->eventQueue->recordEvent($event);
            try {
                $this->getInitializer()->resync();
            } catch (\Throwable $e) {
                throw new \SugarApiException($e->getMessage());
            }
        }

        ConfigurationManager::updateHintConfigEntry(
            HintConstants::HINT_CONFIG_NOTIFICATION,
            $disableNotifications
        );

        return ['status' => '200'];
    }

    /**
     * Create notifications service token
     *
     * Creates a new authentication token for the notifications service.
     *
     * Returns an array (hashmap) of the form:
     * [
     *      accessToken => randomTokenString
     *      ttlMs => time to live of the token in millseconds (just # ms, not a date!)
     *      maxReqPerSec => max permitted requests per second
     * ]
     *
     * Can throw various exceptions if the notifications service cannot be contacted or if it
     * the license key is not present or is invalid
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function createNotificationsServiceToken(ServiceBase $api, array $args)
    {
        $manager = Manager::instance();
        return $manager->createNotificationsServiceAccessToken();
    }

    /**
     * Register instance to company identity endpoint
     *
     * @return array
     */
    public function registerInstanceToCompanyIdentityEndpoint()
    {
        $manager = Manager::instance();
        return $manager->registerCompanyIDToDE();
    }

    /**
     * Register config to enrich bean endpoint
     *
     * @param string $privilegeToken
     * @param array $configDataBeanData
     * @return array
     */
    public function registerConfigToEnrichBeanEndpoint($privilegeToken, $configDataBeanData)
    {
        $manager = Manager::instance();
        return $manager->registerToConfigBean($privilegeToken, $configDataBeanData);
    }

    /**
     * Queue unlicense events
     *
     * Queues UserUnlicensedEvents on 10.3+ Sugar instances to unlicensing users where
     * per-user licensing is supported.
     *
     * @param $immediateFlush : when true, immediately process the queue to send relevant accountset
     * and target data prior to removal of hint tables
     */
    public function queueUnlicenseEvents($immediateFlush = false)
    {
        // On uninstall for instances that support per-user licensing: send UserUnlicensedEvents for all users that have hint licenses.
        $users = \BeanFactory::retrieveBean('Users');
        $query = new \SugarQuery();
        $query->select(['id', 'license_type']);
        $query
            ->from($users)
            ->where()
            ->equals('status', 'Active');
        $rows = $query->execute();

        // Unlicense users who currently have a hint license. On soft uninstalls,
        // this will mark the 'previously_licensed' column for those who
        // previously had a license, allowing for easy revival of their respective
        // targets and accountsets. For hard uninstalls
        foreach ($rows as $row) {
            $isLicensed = strpos($row['license_type'], '"HINT"');
            if ($isLicensed) {
                // We only immediately flush the queue on hard uninstalls, so we can use its flag
                // to determine whether or not we should delete all notification data.
                // This is important because we ONLY delete notification metadata from
                // both the MLP and ISS on hard uninstalls.
                $this->eventQueue->recordEvent(new UserUnlicensedEvent([
                    'userId' => $row['id'],
                    'hadLicense' => true,
                    'deleteData' => $immediateFlush,
                ]));
            }
        }

        if ($immediateFlush) {
            $queueProcessor = $this->getQueueProcessor();
            for ($i = 0; $i < 5; $i++) {
                $queueProcessor->processQueue();
                // If no events are left, simply return. Otherwise, sleep for 10 seconds
                // and try again in a moment.
                if (!$this->eventQueue->getQueuedEvents()) {
                    return;
                }
                sleep(10);
            }
        }
    }

    /**
     * Get Initializer
     *
     * @return Initializer
     */
    protected function getInitializer(): Initializer
    {
        return new Initializer();
    }

    /**
     * Get queue processor
     *
     * @return QueueProcessor
     */
    protected function getQueueProcessor(): QueueProcessor
    {
        return new QueueProcessor();
    }
}
