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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdpConfig;

trait IdmModeLimitationTrait
{
    /**
     * @param string $module
     * @return bool
     */
    public function isLimitedForModuleInIdmMode(string $module): bool
    {
        $idpConfig = $this->getIdpConfig();
        return $idpConfig->isIDMModeEnabled() &&
            in_array($module, $idpConfig->getIDMModeDisabledModules(), true);
    }

    /**
     * @param string $module
     * @param array $fieldDefs
     * @return bool
     */
    public function isLimitedForFieldInIdmMode(string $module, array $fieldDefs): bool
    {
        // UserType field is handled separately in UserViewHelper and MassUpdate
        // and to set idm_mode_disabled=>true for it we need to change code in UserViewHelper and MassUpdate.
        return $this->isLimitedForModuleInIdmMode($module) &&
            (
                $fieldDefs['name'] === 'UserType' ||
                (!empty($fieldDefs['idm_mode_disabled']) &&
                    ($fieldDefs['name'] !== 'license_type' ||
                        ($fieldDefs['name'] === 'license_type' && $this->getUserLicenseTypeIdmModeLock())))
            );
    }

    /**
     * @return IdpConfig
     */
    public function getIdpConfig(): IdpConfig
    {
        return new IdpConfig(\SugarConfig::getInstance());
    }

    /**
     * get license type lock config
     * @return bool
     */
    protected function getUserLicenseTypeIdmModeLock(): bool
    {
        return $this->getIdpConfig()->getUserLicenseTypeIdmModeLock();
    }
}
