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

namespace Sugarcrm\Sugarcrm\Hint;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sugarcrm\Sugarcrm\Hint\Http\Client;
use Sugarcrm\Sugarcrm\Hint\Job\UserInitJob;
use Sugarcrm\Sugarcrm\Hint\Logger\Logger as HintLogger;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\AccountsetAddEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceInitCloneCompletedEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceInitCloneEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceInitEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceResyncEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceDisableNotificationsEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\TargetAddEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UpdateLicenseEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\EventTypes;
use Sugarcrm\Sugarcrm\Hint\Queue\QueueTrait;

class Initializer implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use QueueTrait;

    /**
     * Initializer constructor.
     */
    public function __construct()
    {
        $this->eventQueue = $this->getEventQueue();
        $this->setLogger(new HintLogger());
    }

    /**
     * Init new instance
     */
    public function init()
    {
        $this->logger->info('Started new instance init process');

        // NOTE: Geo configuration for new MLP installations are done in the post_execute
        // script.

        // 1. init instance event
        $this->eventQueue->recordEvent(new InstanceInitEvent());

        // 2. process existing targets
        $this->initTargets();

        // 3. process existing accountsets
        $this->initAccountsets();

        // 4. init new users
        $this->initUsers(EventTypes::INSTANCE_INIT_COMPLETED, 'Finished new instance init process');
    }

    /**
     * Init cloned instance
     *
     * This method is invoked when some change is detected in the identity triple.
     * It implements the policy about which sorts of changes to the triple cause which
     * behaviors in the MLP (just updating the license key, recognizing and creating a
     * new cloned instance, etc).
     *
     * @param array $oldTriple
     * @param array $newTriple
     */
    public function initClonedInstance($oldTriple, $newTriple)
    {
        $cloneCompletionLog = null;
        if ($this->justLicenseChanged($oldTriple, $newTriple)) {
            $this->logger->info('Recording license update change');
            // when the license is changed update the license in ISS and DE.
            $this->sendUpdateLicenseCommand($oldTriple, $newTriple);
            return;
        }

        $this->logger->info('Started cloned instance init process');

        // reset geo for cloned instance
        $this->resetGeo();

        $this->setDEIdentityAndConfig();

        // get state of old instance disableNotifications to use for the clone
        $notificationsRow = ConfigurationManager::getHintConfigEntry(HintConstants::HINT_CONFIG_NOTIFICATION);
        $disableNotifications = $notificationsRow ?
            $notificationsRow['disableNotifications'] : null;

        // 1. clean event queue
        $this->eventQueue->cleanQueue();

        // 2. send clone instance command to ISS along with state of notifications setting
        $this->eventQueue->recordEvent(new InstanceInitCloneEvent([
            'disableNotifications' => $disableNotifications,
        ]));

        $cloneCompletionLog = 'Finished cloned instance init process';

        // If notifications are enabled then perform cleaning and adding notifications events to send to ISS.
        if (!$disableNotifications) {
            // 3. process existing targets
            $this->initTargets();

            // 4. process existing accountsets
            $this->initAccountsets();

            // 5. init new users. Since initUsers is the final step (including the sending
            // of the clone completion event), return early here to prevent sending a duplicate
            // clone completion event.
            $this->initUsers(EventTypes::INSTANCE_INIT_CLONE_COMPLETED, $cloneCompletionLog);
            return;
        }
        //  emit instance init clone completed event
        $this->logger->info($cloneCompletionLog);
        // 6. emit instance init clone completed event
        $this->eventQueue->recordEvent(new InstanceInitCloneCompletedEvent());
    }

    /**
     * Resync instance
     */
    public function resync()
    {
        $this->logger->info('Started instance resync process');

        $this->setDEIdentityAndConfig();

        // 1. clean event queue
        $this->eventQueue->cleanQueue();

        // 2. resync instance event
        $this->eventQueue->recordEvent(new InstanceResyncEvent());

        // 3. process existing targets
        $this->initTargets();

        // 4. process existing accountsets
        $this->initAccountsets();

        // 5. init new users
        $this->initUsers(EventTypes::INSTANCE_RESYNC_COMPLETED, 'Finished instance resync process');
    }

    /**
     * Goes through all existing targets and puts them back to the queue
     */
    protected function initTargets()
    {
        $offsetStep = 100;
        $offset = 0;

        if ($this->isValidTable('hint_notification_targets', 'HintNotificationTargets')) {
            while (true) {
                $moduleName = 'HintNotificationTargets';
                $objectName = 'HintNotificationTarget';
                if (!$this->vardefsReady($moduleName, $objectName)) {
                    throw new \Exception("Vardefs for the {$moduleName} module could not be refreshed.");
                }

                $seed = \BeanFactory::newBean($moduleName);
                $query = new \SugarQuery();
                $query->select([
                    ['id', 'target_id'],
                    'type',
                    'credentials',
                    ['assigned_user_id', 'user_id'],
                ]);
                $query->from($seed, ['alias' => 'targets']);
                $query->join('assigned_user_link', [
                    'joinType' => 'INNER',
                ]);
                $query->orderBy('date_entered', 'ASC');
                $query->offset($offset);
                $query->limit($offsetStep);


                $rows = $query->execute();

                // no valid targets - stop processing
                if (!$rows) {
                    return;
                }

                $offset += $offsetStep;

                // Gets a list of all active users.
                $userBean = \BeanFactory::newBean('Users');
                $query = new \SugarQuery();
                $query->select(['*']);
                $query->from($userBean);
                $query->where()
                    ->equals('status', 'Active');
                $userRows = $query->execute();

                // If the sugar instance supports per-user licensing, only send
                // addTarget events for users who are currently licensed.
                $users = [];
                foreach ($userRows as $userRow) {
                    $users[] = $userRow;
                }

                // map sql result set to TARGET_ADD event payloads
                $data = [];
                foreach ($rows as $row) {
                    $row = array_change_key_case($row, CASE_LOWER);
                    // filters users based on Active status.
                    if (array_search($row['user_id'], array_column($users, 'id')) === false) {
                        continue;
                    }

                    $data[] = array_merge($row, [
                        // event queue expects decoded data
                        'credentials' => $this->getCorrectSiteURL($row['credentials']),
                    ]);
                }

                // queue TARGET_ADD events with proper payload and user context
                // keep map of target ids to prevent duplicate addTarget events
                // from being sent
                $seenTargetIds = [];
                foreach ($data as $eventData) {
                    $curTargetId = $eventData['target_id'];
                    if (isset($seenTargetIds[$curTargetId])) {
                        continue;
                    }
                    $seenTargetIds[$curTargetId] = true;
                    $eventData = array_merge(['targetId' => $curTargetId], $eventData);
                    $context = ['user_id' => $eventData['user_id']];
                    $this->eventQueue->recordEvent(new TargetAddEvent($eventData), $context);
                }
            }
        }
    }

    /**
     * Init accountsets
     *
     * Goes through all existing accountsets and puts them back to the queue
     */
    protected function initAccountsets()
    {
        $offsetStep = 100;
        $offset = 0;

        if ($this->isValidTable('hint_accountsets', 'HintAccountsets')) {
            while (true) {
                $moduleName = 'HintAccountsets';
                $objectName = 'HintAccountset';
                if (!$this->vardefsReady($moduleName, $objectName)) {
                    throw new \Exception("Vardefs for the {$moduleName} module could not be refreshed.");
                }

                $seed = \BeanFactory::newBean($moduleName);
                $idQuery = new \SugarQuery();
                $idQuery->select(['id']);
                $idQuery->from($seed);
                $idQuery->orderBy('date_entered', 'ASC');
                $idQuery->offset($offset);
                $idQuery->limit($offsetStep);

                $ids = array_column($idQuery->execute(), 'id');

                // no valid accountsets - stop processing
                if (!$ids) {
                    return;
                }

                $offset += $offsetStep;

                $query = new \SugarQuery();
                $query->from($seed, ['alias' => 'accountsets']);
                $query->join('assigned_user_link', [
                    'joinType' => 'INNER',
                    'alias' => 'users',
                ]);
                $query->join('notification_targets', [
                    'joinType' => 'left',
                    'alias' => 'targets',
                ]);
                $query->join('tag_link', [
                    'joinType' => 'left',
                    'alias' => 'tags',
                ]);
                // we define join aliases before select
                $query->select([
                    // accountset fields
                    ['id', 'accountset_id'],
                    'type',
                    'category',
                    ['assigned_user_id', 'user_id'],
                    // related target and tag ids
                    ['targets.id', 'target_id'],
                    ['tags.id', 'tag_id'],
                ]);
                $query->where()->in('accountsets.id', $ids);

                $accountsetRows = $query->execute();

                // no valid accountsets - stop processing
                if (!$accountsetRows) {
                    return;
                }

                // Per-user licensing: if user is not currently licensed, skip them.
                // If not per-user licensing, then we just use the accountset rows
                // returned from the original accountsets query.
                $rows = [];
                // Gets a list of all active users.
                $userBean = \BeanFactory::newBean('Users');
                $query = new \SugarQuery();
                $query->select(['*']);
                $query->from($userBean);
                $query->where()
                    ->equals('status', 'Active');
                $userRows = $query->execute();

                // Build map of user ids to current licenses the user holds.
                $userIdLicenseMap = [];
                foreach ($userRows as $user) {
                    if (!isset($userIdLicenseMap[$user['id']])) {
                        $userIdLicenseMap[$user['id']] = $user['license_type'];
                    }
                }

                // Only want to add accountset rows for users who are currently licensed and active
                foreach ($accountsetRows as $accountset) {
                    // Some old accountsets may exist for inactive users, non-licensed users, etc.
                    // They may not have been mapped in the previous step (because of being inactive/unlicensed),
                    // so we make sure the entry id is set.
                    if (!isset($userIdLicenseMap[$accountset['user_id']])) {
                        continue;
                    }
                    $rows[] = $accountset;
                }

                // map sql result set (denormalized data) to ACCOUNTSET_ADD event payloads
                $data = [];
                foreach ($rows as $row) {
                    $row = array_change_key_case($row, CASE_LOWER);
                    $id = $row['accountset_id'];

                    // prepare pure event payload (accountset + empty arrays of related ids)
                    if (!isset($data[$id])) {
                        $data[$id] = array_merge(
                            array_intersect_key($row, array_flip(['accountset_id', 'type', 'category', 'user_id'])),
                            ['targetIds' => [], 'tagIds' => []]
                        );
                    }

                    // add related ids
                    if ($row['target_id'] && !safeInArray($row['target_id'], $data[$id]['targetIds'])) {
                        $data[$id]['targetIds'][] = $row['target_id'];
                    }

                    if ($row['tag_id']) {
                        $data[$id]['tagIds'][] = $row['tag_id'];
                    }
                }

                // queue ACCOUNTSET_ADD events with proper payload and user context
                foreach ($data as $eventData) {
                    $eventData = array_merge(['accountsetId' => $eventData['accountset_id']], $eventData);
                    $context = ['user_id' => $eventData['user_id']];
                    $this->eventQueue->recordEvent(new AccountsetAddEvent($eventData), $context);
                }
            }
        }
    }

    /**
     * Init users
     *
     * Finds users with no accountsets and delegates further
     * processing to UserInitJob. Since this is the last step for recording a new
     * instance, cloning an instance, and resyncing an instance, we delegate the
     * sending of the completion event here.
     */
    protected function initUsers($completionEventType, $logMessage)
    {
        // This will create new accountsets for users that do not have them
        // (i.e. newly created users without hint licenses). We skip this when per-user licensing
        // is supported (Sugar 10.3+), as the HintSeatsJob will handle this -- don't want to
        // accidentally create notification data for non-licensed users.
        $this->logger->info($logMessage);
        $this->eventQueue->recordEvent(HintConstants::getCompletionEvent($completionEventType));

        $explicitlyDisabledNotifications = ConfigurationManager::getHintConfigEntry(HintConstants::HINT_CONFIG_NOTIFICATION);

        // If notifications were explicitly disabled when a previous upgrade
        // occurred, make sure that this new instance will also be set as explicitly
        // disabled. If notifications are desired to continue again, an admin will
        // need to manually go into the Hint Configuration panel and enable them.
        if ($explicitlyDisabledNotifications['value']) {
            $disableEvent = new InstanceDisableNotificationsEvent(['explicitDisable' => true]);
            $this->eventQueue->recordEvent($disableEvent);
        }

        $ids = $this->getNotInitializedUserIds();
        $scheduler = $this->getUserInitScheduler();
        if ($ids && $scheduler) {
            // delete existing user init jobs
            $jobQueueTable = \BeanFactory::getBean('SchedulersJobs')->getTableName();
            \DBManagerFactory::getConnection()->delete($jobQueueTable, [
                'name' => UserInitJob::NAME,
            ]);

            // Add the completion event to the final UserInitJob chunk, so that
            // we can give the final UserInitJob chunk the completion event to send.
            $userIdChunks = array_chunk($ids, UserInitJob::ID_CHUNK_SIZE);
            for ($i = 0; $i < safeCount($userIdChunks); $i++) {
                $chunk = $userIdChunks[$i];
                $job = $scheduler->createJob();
                $job->status = \SchedulersJob::JOB_STATUS_QUEUED;
                $job->resolution = \SchedulersJob::JOB_PENDING;

                // If the final chunk: add the completion event to it along with user ids
                // else: just pass user ids of the current chunk
                if ($i == safeCount($userIdChunks) - 1) {
                    $job->data = json_encode([
                        'ids' => $chunk,
                        'completionEventData' => [
                            'event' => $completionEventType,
                            'log' => $logMessage,
                        ],
                    ]);
                } else {
                    $job->data = json_encode([
                        'ids' => $chunk,
                        'completionEventData' => null,
                    ]);
                }
                $job->save();
            }

            // When this block executes, we know that there were users that we set up to be
            // initialized. Because of this, the UserInitJob will have been created, and
            // will send the proper completion event on its success, so return here.
            return;
        }

        // If execution makes it here, then we know that no new UserInitJob(s) were created,
        // so we send the proper completion event here.
        $this->logger->info($logMessage);
        $this->eventQueue->recordEvent(HintConstants::getCompletionEvent($completionEventType));
    }

    /**
     * Returns ids of all users who do NOT have an accountset
     *
     * @return array
     * @throws \SugarQueryException
     */
    protected function getNotInitializedUserIds()
    {
        $accountset = \BeanFactory::newBean('HintAccountsets');
        $subquery = new \SugarQuery();
        $subquery->select(['assigned_user_id']);
        $subquery->distinct(true);
        $subquery->from($accountset);

        $user = \BeanFactory::newBean('Users');
        $query = new \SugarQuery();
        $query->select(['id']);
        // this one results to warning due to "notIn" not checking param types
        // $query->from($user)->where()->notIn('id', $subquery);
        $query->from($user)->where()->condition('id', 'NOT IN', $subquery);

        // query for non-inactive users and users that haven't been deleted
        $nonInactiveUsersQuery = new \SugarQuery();
        $nonInactiveUsersQuery->select(['id']);
        // using status Active should screen out fake users like the snip user
        $nonInactiveUsersQuery->from($user)->where()->condition('status', '=', 'Active');
        $nonInactiveUsersQuery->from($user)->where()->queryAnd()->equals('deleted', '0');

        $assignedUserIds = array_column($query->execute(), 'id');
        $nonInactiveUserIds = array_column($nonInactiveUsersQuery->execute(), 'id');

        // return intersection of two user id queries to disavow inactive users
        $ids = array_intersect($assignedUserIds, $nonInactiveUserIds);
        return $ids;
    }

    /**
     * Returns user init scheduler
     *
     * @return \Scheduler|null
     * @throws \SugarQueryException
     */
    protected function getUserInitScheduler()
    {
        $job = sprintf('class::%s', UserInitJob::class);
        $seed = \BeanFactory::newBean('Schedulers');

        $query = new \SugarQuery();
        $query->from($seed)->where()->equals('job', $job);

        $schedulers = $seed->fetchFromQuery($query, ['id', 'name', 'job']);

        return array_shift($schedulers);
    }

    /**
     * Just license changed
     *
     * Predicate that returns true if the only difference between the old and new
     * identity triples is the license key.  Special case if the old license key
     * is null, in that case it shouldn't be treated as a change
     * REMIND: is that true???
     *
     * @param $oldTriple identity triple; an object with three keys license_key, unique_key, and
     *                   site_url.  This is the "old" triple; the one from before the change
     *                   was detected
     * @param $newTriple identity triple.  This is the triple that contains at least one difference
     *                   from the old triple.
     * @return true iff just the licence key changed between the two triples, false in all other
     *         cases.
     */
    private function justLicenseChanged($oldTriple, $newTriple)
    {
        return $oldTriple['license_key'] !== $newTriple['license_key'] &&
            $oldTriple['unique_key'] === $newTriple['unique_key'] && $oldTriple['site_url'] === $newTriple['site_url'];
    }

    /**
     * Send update license command
     *
     * Called when the license key (only) has changed.  This will cause an updateLicense command
     * to be sent to ISS and also it will update the license in the DE.
     *
     * @param array $oldTriple
     * @param array $newTriple
     */
    private function sendUpdateLicenseCommand($oldTriple, $newTriple)
    {
        $this->eventQueue->recordEvent(new UpdateLicenseEvent([
            'oldTriple' => $oldTriple,
            'newTriple' => $newTriple,
        ]));

        $manager = Manager::instance();
        // The following will make a call to the /updateLicense endpoint and update the license key.
        $stage2client = new Client($manager->serviceUrl);
        $body = [
            'uniqueKey' => $oldTriple['unique_key'],
            'oldCompanyId' => $oldTriple['license_key'],
            'newCompanyId' => $newTriple['license_key'],
            'siteURL' => $oldTriple['site_url'],
        ];
        $stage2client->updateLicenseInDataEnrichmentIdentityTable($body);
    }

    private function isValidTable($tableName, $moduleName)
    {
        $tableExists = $GLOBALS['db']->tableExists($tableName);
        if (!$tableExists) {
            return false;
        }
        $doesTableContainRecord = ConfigurationManager::doesTableContainsRows($moduleName);
        return $tableExists && $doesTableContainRecord;
    }

    /**
     * Get correct site url
     *
     * Called to return correct SiteURL in case of any change in it due to cloning.
     * @param string $targetEntry credential triple; an object with three keys email, timezone and siteUrl.
     * @return array credential Object with correct siteURL in use by Sugar.
     */
    private function getCorrectSiteURL($targetEntry)
    {
        $userTargets = json_decode($targetEntry, true);
        if (is_array($userTargets) && isset($userTargets['siteUrl'])) {
            $existentSiteURL = $userTargets['siteUrl'];
            $currentSiteURL = \SugarConfig::getInstance()->get('site_url');
            if ($existentSiteURL && ($existentSiteURL !== $currentSiteURL)) {
                return [
                    'email' => $userTargets['email'],
                    'timezone' => $userTargets['timezone'],
                    'siteUrl' => $currentSiteURL,
                ];
            }
        }
        return $userTargets;
    }

    /**
     * Reset geo
     *
     * Reset the geo config. By nullifying the geo config entry, the manager will be forced
     * to recompute the geo config for the services when it's initialized.
     * Since the Manager instance is a shared singleton instance, simply calling its instance()
     * method will re-initialize the shared instance (including resetting the geo configuration
     * of the services, since we null it out here prior to re-initialization).
     */
    private function resetGeo()
    {
        ConfigurationManager::updateHintConfigEntry(HintConstants::HINT_CONFIG_GEO, null);
        Manager::instance();
    }

    /**
     * Set DE identity and config
     * Force a createIdentity followed by (if successful) set config upon DE
     */
    private function setDEIdentityAndConfig()
    {
        // REMIND: should this identity be conditional on what happens here?
        if ($this->setDEIdentity()) {
            $this->setDEFieldConfig();
        }
    }

    /**
     * Set DE identity
     *
     * Register the current Sugar instance identity triple with DE, and return true if successful.
     *
     * @return bool true means DE either successfully had an identity created or it already had
     *          the identity registered.
     */
    private function setDEIdentity()
    {
        $hintApiCalls = new \HintApi();

        // send the triples to DE.
        $identityCreatedStatus = $hintApiCalls->registerInstanceToCompanyIdentityEndpoint();
        // conflicts are ok here
        if ($identityCreatedStatus['status'] === 200 || $identityCreatedStatus['status'] == 409) {
            return true;
        }

        return false;
    }

    /**
     * Set DE field config
     *
     * Unconditionally updates DE's field configuration information.  The invoked APIs
     * will provide logging on error.
     */
    private function setDEFieldConfig()
    {
        $hintApiCalls = new \HintApi();

        // grab the access token.
        $hintApiCalls->createToken('', '');
        // get the enrich fields config bean
        $configDataBeanData = \HintEnrichFieldConfig::getHintEnrichFieldConfigBean();
        // store the config bean in DE with privilegeToken.
        $hintApiCalls->registerConfigToEnrichBeanEndpoint($hintApiCalls->privilegeToken, json_decode($configDataBeanData['config_data']));
    }

    /**
     * Vardefs ready
     *
     * Checks to see if the provided module is currently being refreshed, and will wait
     * a moment before retrying if so.
     * @param $moduleName string
     * @param $objectName string
     *
     * @return bool Return true if the module vardefs are ready, false if otherwise.
     */
    private function vardefsReady($moduleName, $objectName)
    {
        $guard = "{$moduleName}:{$objectName}";
        for ($i = 0; $i < 15; $i++) {
            if (isset(\VardefManager::$inReload[$guard])) {
                sleep(1);
                continue;
            }
            return true;
        }
        return false;
    }
}
