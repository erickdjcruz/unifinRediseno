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

namespace Sugarcrm\Sugarcrm\CustomerJourney;

use Sugarcrm\Sugarcrm\CustomerJourney\Exception as Exception;

class ConfigurationManager
{
    /**
     * Ensure current user has admin permissions
     *
     * @return boolean
     * @throws \UserNotAuthorizedException
     */
    public static function ensureAutomateUser()
    {
        if (!hasAutomateLicense()) {
            throw new Exception\UserNotAuthorizedException('LBL_CUSTOMER_JOURNEY_ERROR_USER_MISSING_ACCESS');
        }

        return true;
    }

    /**
     * Ensure current user has admin permissions
     *
     * @return boolean
     * @throws \SugarApiExceptionNotAuthorized
     */
    public static function ensureAdminUser()
    {
        global $current_user, $app_strings;

        if (!$current_user->isAdmin()) {
            throw new \SugarApiExceptionNotAuthorized($app_strings['EXCEPTION_NOT_AUTHORIZED']);
        }

        return true;
    }
}
