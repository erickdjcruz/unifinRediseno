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

namespace Sugarcrm\Sugarcrm\UserUtils\Managers;

use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerBasePayload;
use Sugarcrm\Sugarcrm\Util\Uuid;
use SugarQuery;

class GeneralManager extends Manager
{
    /**
     * The source user
     * @var string
     */
    private $sourceUser;

    /**
     * The destination users
     *
     * @var array
     */
    private $destinationUsers;

    /**
     * Shows if should use a schedule job
     *
     * @var bool
     */
    protected $useScheduledJob = false;

    /**
     * Constructor
     *
     * @param InvokerBasePayload $payload
     */
    public function __construct(InvokerBasePayload $payload)
    {
        $this->payload = $payload;
        $this->sourceUser = $payload->getSourceUser();
        $this->destinationUsers = $payload->getDestinationUsers();

        if (safeCount($this->destinationUsers) > self::MAX_USER) {
            $this->useScheduledJob = true;
        }
    }

    /**
     * Clones the favorite reports from a source user
     * to a list of users.
     */
    public function cloneFavoriteReports(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destUserId) {
            $this->deleteFavoriteReports($destUserId);
            $this->insertFavoriteReports($this->sourceUser, $destUserId);
        }
    }

    /**
     * Deletes favorite reports for a user
     *
     * @param string $userId
     */
    public function deleteFavoriteReports(string $userId): void
    {
        $db = \DBManagerFactory::getInstance();

        $favoritesBean = \BeanFactory::newBean('SugarFavorites');
        $fieldDefs = $favoritesBean->getFieldDefinitions();
        $db->updateParams($favoritesBean->getTableName(), $fieldDefs, [
            'deleted' => '1',
        ], [
            'module' => 'Reports',
            'assigned_user_id' => $userId,
        ]);
    }

    /**
     * Insert favorite reports from a user to another
     *
     * @param string $sourceUserId
     * @param string $destUserId
     */
    public function insertFavoriteReports(string $sourceUserId, string $destUserId): void
    {
        $db = \DBManagerFactory::getInstance();

        $favoritesBean = \BeanFactory::newBean('SugarFavorites');
        $fieldDefs = $favoritesBean->getFieldDefinitions();

        $sourceFavorites = $this->getUserFavoriteReports($sourceUserId);
        foreach ($sourceFavorites as $favorite) {
            $favorite['id'] = Uuid::uuid4();
            $favorite['assigned_user_id'] = $destUserId;
            $favorite['created_by'] = $destUserId;
            $favorite['modified_user_id'] = $destUserId;
            $db->insertParams($favoritesBean->getTableName(), $fieldDefs, $favorite);
        }
    }

    /**
     * Retrieves favorite reports for a certain user
     *
     * @param string $userId
     * @return array
     */
    public function getUserFavoriteReports(string $userId): array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('SugarFavorites'), ['team_security' => false]);
        $sugarQuery->where()->equals('assigned_user_id', $userId);
        $sugarQuery->where()->equals('module', 'Reports');
        $sugarQuery->where()->equals('deleted', 0);
        $result = $sugarQuery->execute();

        return $result;
    }

    /**
     * Clones the default teams
     */
    public function cloneDefaultTeams(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        $sourceUser = \BeanFactory::retrieveBean('Users', $this->sourceUser);
        $teamId = $sourceUser->team_id;
        $teamsSetId = $sourceUser->team_set_id;

        foreach ($this->destinationUsers as $destUserId) {
            $targetUser = \BeanFactory::retrieveBean('Users', $destUserId);
            if ($targetUser) {
                $targetUser->team_id = $teamId;
                $targetUser->team_set_id = $teamsSetId;
                $targetUser->save();
            }
        }
    }

    /**
     * Clones the navigation bar module selection
     */
    public function cloneNavigationBarModuleSelection(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        $sourceUser = \BeanFactory::retrieveBean('Users', $this->sourceUser);
        $displayPref = $sourceUser->getPreference('display_tabs');
        $hidePref = $sourceUser->getPreference('hide_tabs');

        foreach ($this->destinationUsers as $destUserId) {
            $targetUser = \BeanFactory::retrieveBean('Users', $destUserId);
            if ($targetUser) {
                $targetUser->setPreference('display_tabs', $displayPref);
                $targetUser->setPreference('hide_tabs', $hidePref);
                $targetUser->savePreferencesToDB();
            }
        }
    }

    /**
     * Clones the notify on assigment option
     */
    public function cloneNotifyOnAssignment(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        $sourceUser = \BeanFactory::retrieveBean('Users', $this->sourceUser);
        $receiveNotifications = $sourceUser->receive_notifications;

        foreach ($this->destinationUsers as $destUserId) {
            $destinationUser = \BeanFactory::retrieveBean('Users', $destUserId);
            if ($destinationUser) {
                $destinationUser->receive_notifications = $receiveNotifications;
                $destinationUser->save();
            }
        }
    }

    /**
     * Clones the reminder options
     */
    public function cloneReminderOptions(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        $sourceUser = \BeanFactory::retrieveBean('Users', $this->sourceUser);
        if (!$sourceUser) {
            return;
        }

        $reminderChecked = $sourceUser->getPreference('reminder_checked');
        $reminderTime = $sourceUser->getPreference('reminder_time');
        $emailReminderChecked = $sourceUser->getPreference('email_reminder_checked');
        $emailReminderTime = $sourceUser->getPreference('email_reminder_time');

        foreach ($this->destinationUsers as $destUserId) {
            $destinationUser = \BeanFactory::retrieveBean('Users', $destUserId);
            if ($destinationUser) {
                $destinationUser->setPreference('reminder_checked', $reminderChecked);
                $destinationUser->setPreference('reminder_time', $reminderTime);
                $destinationUser->setPreference('email_reminder_checked', $emailReminderChecked);
                $destinationUser->setPreference('email_reminder_time', $emailReminderTime);
                $destinationUser->savePreferencesToDB();
            }
        }
    }


    /**
     * Clones the favorite reports from a source user
     * to a list of users.
     */
    public function cloneSugarEmailClient(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        $sourceUser = \BeanFactory::retrieveBean('Users', $this->sourceUser);
        $emailLinkType = $sourceUser->getPreference('email_link_type');

        foreach ($this->destinationUsers as $destUserId) {
            $targetUser = \BeanFactory::retrieveBean('Users', $destUserId);
            if ($targetUser) {
                $targetUser->setPreference('email_link_type', $emailLinkType);
                $targetUser->savePreferencesToDB();
            }
        }
    }
}
