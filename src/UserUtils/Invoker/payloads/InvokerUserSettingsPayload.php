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

namespace Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads;

/**
 * The InvokerUserSettingsPayload class handles payloads related to user settings
 */
class InvokerUserSettingsPayload extends InvokerBasePayload
{
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->userSettings = $options['userSettings'] ?? [];
    }

    /**
     * Getter for the user settings
     * @return array
     */
    public function getUserSettings(): array
    {
        return $this->userSettings;
    }

    /**
     * Setter for the user settings
     * @param array $userSettings
     */
    public function setUserSettings(array $userSettings): void
    {
        $this->userSettings = $userSettings;
    }
}
