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

namespace Sugarcrm\Sugarcrm\Hint\Queue\Event;

use Sugarcrm\Sugarcrm\Hint\Queue\EventTypes;
use Sugarcrm\Sugarcrm\Hint\Manager;

class UserUnlicensedEvent extends BaseUserLicenseChangeEvent
{
    /**
     * {@inheritDoc}
     */
    public function toQueueRows(): array
    {
        // Tell the ISS to delete all notification metadata on hard uninstalls/explicit
        // license removal from User Management module.
        if ($this->data['deleteData']) {
            $this->removeLicense();
            return $this->baseToQueueRows(EventTypes::ACCOUNTSET_DELETE_ALL, EventTypes::TARGET_DELETE_ALL);
        }

        return [];
    }

    /**
     * Remove license
     */
    private function removeLicense(): void
    {
        $user = \BeanFactory::retrieveBean('Users', $this->data['userId']);
        $hadLicense = $this->data['hadLicense'];

        // per-user licensing only sugar 10.3+
        if ($hadLicense) {
            $licenses = json_decode($user->license_type, true);
            if (is_array($licenses)) {
                $key = array_search('HINT', $licenses);
                if ($key !== false) {
                    unset($licenses[$key]);
                    $user->license_type = json_encode(array_values($licenses));
                }
            }

            // Mark this column as true for this user. In the case that they become licensed
            // again in the future, we can check this column which will tell us that this user
            // has old accountsets & targets that we can simply enable again by communicating that
            // to the ISS. We save this to the DB after adding events to the ISS command queue.
            $user->previously_licensed = true;
            $user->save();
        }
    }
}
