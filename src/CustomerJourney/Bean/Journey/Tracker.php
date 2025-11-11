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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey;

use User;

class Tracker
{
    /**
     * Keeping the track of user's activeness
     *
     * @param User $user
     * @throws \SugarApiExceptionInvalidParameter
     * @throws \SugarApiExceptionLicenseSeatsNeeded
     * @throws \SugarApiExceptionNotAuthorized
     */
    public static function activeUser(User $user)
    {
        $timeDate = \TimeDate::getInstance();
        $date = $timeDate->getNow()->modify('- 8 hours');

        if (!empty($user->customer_journey_last_active)) {
            $lastActive = $timeDate->fromDb($user->customer_journey_last_active) ?: $timeDate->fromUser($user->customer_journey_last_active);

            if ($lastActive < $date) {
                $user->customer_journey_last_active = $timeDate->nowDb();
                $user->update_date_modified = false;
                $user->save();
            }
        } else {
            $user->customer_journey_last_active = $timeDate->nowDb();
            $user->update_date_modified = false;
            $user->save();
        }
    }
}
