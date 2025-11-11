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

namespace Sugarcrm\Sugarcrm\modules\Users;

use Sugarcrm\Sugarcrm\Hint\LogicHook\LogicHook;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UserDeleteEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UserInactiveEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UserEmailUpdateEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UserLicensedEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\UserUnlicensedEvent;
use Sugarcrm\Sugarcrm\modules\HintNotificationTargets\NotificationTargetTypes;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Hint\Queue\Queue;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\TargetAddEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\AccountsetAddEvent;
use Sugarcrm\Sugarcrm\Hint\Manager;

class HintUsersHook extends LogicHook
{
    /**
     * @param $user
     * @param $event
     * @param $arguments
     * @throws \Exception
     */
    public function afterSave($user, $event, $arguments)
    {
        if (!$user->isLicensedForHint()) {
            return;
        }

        $manager = Manager::instance();

        // new user; only create accountsets, targets if they have a HINT licensed assigned
        if (!$arguments['isUpdate']) {
            \HintAccountset::createUserAccountset($user);

            return;
        }

        // In the 5.4.0 case, this flag is used to ensure that a UserUnlicensedEvent is sent on a
        // user going inactive. This is so that inactive users are not holding onto hint licenses,
        // and to prevent potentially strange scenarios occuring if this user were to become active
        // again (i.e. had a license, went inactive but kept license, now they're active but suddenly
        // there are not enough hint licenses anymore per hint seats, yet they have a license. This is
        // a contradiction).
        $removeLicense = false;

        // user becomes Hint licensed, unlicensed, or has become inactive post 5.4.0
        if (!empty($arguments['dataChanges']['license_type']) || $removeLicense) {
            $license = $arguments['dataChanges']['license_type'];

            $before = $license['before'] ?: '[]';
            $oldData = is_array($before) ? $before : json_decode($before, true);
            $oldLicensedUser = $this->isHintUser($oldData);

            $after = $license['after'] ?: '[]';

            $newData = is_array($after) ? $after : json_decode($after, true);
            $newLicensedUser = $this->isHintUser($newData);
            if (!$oldLicensedUser && $newLicensedUser) {
                // If they were previously licensed, revive their old accountsets and targets.
                // Otherwise, they were not previously licensed, so we create accountsets and targets.
                $userId = $user->id;
                $userBean = \BeanFactory::retrieveBean('Users', $userId);

                // prevents re-creation of already added users.
                $userAccountsetsRows = $manager->retrieveUserData('HintAccountsets', $userId);
                if ($userBean->previously_licensed || $userAccountsetsRows) {
                    $manager->reviveAccountsetsAndTargets($user->id);
                } else {
                    \HintAccountset::createUserAccountset($user);
                }
            } elseif (($oldLicensedUser && !$newLicensedUser) || $removeLicense) {
                // NOTE: UserUnlicensedEvent adds to the ISS command queue the following events:
                // [EventTypes::ACCOUNTSET_DELETE_ALL, EventTypes::TARGET_DELETE_ALL]
                //
                // User is getting their license explicitly removed in this case, so we
                // send delete accountsets/targets commands to the ISS and mark the user
                // as previously_licensed (in case they become re-licensed later).
                //
                // This will prevent duplicate accountsets/target deletion events from being
                // sent after a user save occurs in the UserUnlicensedEvent. That save triggers
                // this hook, so this essentially just blocks this potential cycle of
                // UserUnlicensedEvent <-> HintUserHook afterSave().
                if (!$user->previously_licensed) {
                    $this->eventQueue->recordEvent(new UserUnlicensedEvent([
                        'userId' => $user->id,
                        'hadLicense' => true,
                        'deleteData' => true,
                    ]));
                }
            }
            return;
        }

        // new email
        if (!empty($arguments['dataChanges']['email'])) {
            $email = $arguments['dataChanges']['email'];
            $oldPrimaryEmail = $this->getPrimaryEmail($email['before'] ?: []);
            $newPrimaryEmail = $this->getPrimaryEmail($email['after'] ?: []);
            if ($oldPrimaryEmail !== $newPrimaryEmail) {
                $this->updateEmailTargets($user, $newPrimaryEmail);
            }
            return;
        }

        // new email (legacy save)
        if (!empty($arguments['dataChanges']['email1'])) {
            $email = $arguments['dataChanges']['email1'];
            $before = $email['before'] ?: '';
            $after = $email['after'] ?: '';
            if ($before !== $after) {
                $this->updateEmailTargets($user, $after);
            }
            return;
        }

        /*
         * user was deleted from Employees detail view
         * NOTE: if "delete" is initiated from Users detail view
         * "beforeDelete" is triggered
         */
        if (!empty($arguments['dataChanges']['deleted']['after'])) {
            $request = InputValidation::getService();
            if ('Employees' === $request->getValidInputRequest('module', 'Assert\Mvc\ModuleName')
                && 'delete' === $request->getValidInputRequest('action')) {
                $this->eventQueue->recordEvent(new UserDeleteEvent([
                    'userId' => $user->id,
                ]));
                return;
            }
        }
    }

    /**
     * @param $user
     * @param $event
     * @param $arguments
     */
    public function beforeDelete($user, $event, $arguments)
    {
        $this->eventQueue->recordEvent(new UserDeleteEvent([
            'userId' => $user->id,
        ]));
    }

    /**
     * Find primary email in all user emails
     *
     * @param array $emails
     * @return string
     */
    private function getPrimaryEmail(array $emails)
    {
        foreach ($emails as $email) {
            if (!empty($email['primary_address']) && !empty($email['email_address'])) {
                return $email['email_address'];
            }
        }

        return '';
    }

    /**
     * Find user is Hint licensed or not
     *
     * @param array $licenses
     * @return boolean
     */
    private function isHintUser(array $licenses)
    {
        return array_search('HINT', $licenses) !== false;
    }

    /**
     * Adds a record to the queue, finds and updates existing email targets
     *
     * @param $person
     * @param $email
     * @throws \Exception
     */
    private function updateEmailTargets($person, $email)
    {
        $user = $person;
        if ($person instanceof \Employee) {
            $user = \BeanFactory::retrieveBean('Users', $user->id);
        }

        $db = \DBManagerFactory::getInstance();
        $seed = \BeanFactory::newBean('HintNotificationTargets');

        // data
        $timezone = \TimeDate::userTimezone($user);
        $credentials = json_encode([
            'email' => $email,
            'timezone' => $timezone,
            'siteUrl' => \SugarConfig::getInstance()->get('site_url'),
        ], JSON_UNESCAPED_SLASHES);
        // quote types
        $types = [];
        foreach (NotificationTargetTypes::getEmailTypes() as $type) {
            $types[] = $db->quoted($type);
        }

        $builder = $db->getConnection()->createQueryBuilder();
        $query = $builder->update($seed->getTableName())
            ->set('credentials', $db->quoted($credentials))
            ->where($builder->expr()->eq('assigned_user_id', $db->quoted($user->id)))
            ->andWhere($builder->expr()->in('type', $types))
            ->andWhere('deleted = 0');
        $query->execute();

        // add a record to the queue
        $this->eventQueue->recordEvent(new UserEmailUpdateEvent([
            'userId' => $person->id,
            'newEmailAddress' => $email,
            'newTimezone' => $timezone,
        ]));
    }
}
