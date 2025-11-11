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

use BeanFactory;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerUserSettingsPayload;

class UserSettingsManager extends Manager
{
    /**
     * @var mixed[]|mixed
     */
    public $userSettings;
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
     * @param InvokerUserSettingsPayload $payload
     */
    public function __construct(InvokerUserSettingsPayload $payload)
    {
        $this->payload = $payload;
        $this->destinationUsers = $payload->getDestinationUsers();
        $this->userSettings = $payload->getUserSettings();

        if (safeCount($this->destinationUsers) > self::MAX_USER) {
            $this->useScheduledJob = true;
        }
    }

    /**
     * Clones user settings.
     */
    public function cloneUserSettings(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destinationUserId) {
            $this->setUserSettings($destinationUserId, $this->userSettings);
        }
    }

    /**
     * Sets settings to user
     * @param string $destinationUserId
     * @param array $userSettings
     */
    public function setUserSettings($destinationUserId, $userSettings)
    {
        $destinationUser = BeanFactory::retrieveBean('Users', $destinationUserId);

        if (!$destinationUser) {
            return;
        }

        foreach ($userSettings as $settingName => $settingValue) {
            $destinationUser->setPreference($settingName, $settingValue, 0, 'global');
        }

        $destinationUser->savePreferencesToDB();
    }
}
