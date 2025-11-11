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

namespace Sugarcrm\Sugarcrm\modules\HealthCheck\Scanner\Checks;

use UpgradeDriver;

class PasswordHashAlgo
{
    public bool $isError = false;

    public function check(?UpgradeDriver $upgradeDriver): bool
    {
        if ($upgradeDriver instanceof UpgradeDriver) {
            $config = $upgradeDriver->config;
            $sugarVersion = $upgradeDriver->context['versionInfo'][0] ?? '';
            $versionForError = '14.2.0';
        } else {
            $config = $this->getGlobalConfig();
            $sugarVersion = $this->getGlobalVersion();
            $versionForError = '14.1.0';
        }

        if (isset($config['passwordHash'])) {
            if ($sugarVersion && version_compare($sugarVersion, $versionForError, '>=')) {
                $this->isError = true;
            }

            return false;
        }

        return true;
    }

    public function getGlobalConfig(): array
    {
        return $GLOBALS['sugar_config'] ?? [];
    }

    public function getGlobalVersion(): string
    {
        return $GLOBALS['sugar_version'] ?? '';
    }
}
