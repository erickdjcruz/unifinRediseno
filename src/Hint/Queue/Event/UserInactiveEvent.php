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

class UserInactiveEvent extends UserDeleteEvent
{
    /**
     * Get Hint Accountset IDs by user id
     *
     * @param string $userId
     * @return array
     * @throws \SugarQueryException
     */
    protected function getAccountsetIdsByUserId($userId)
    {
        $query = new \SugarQuery();
        $query->select(['id']);
        $query
            ->from(\BeanFactory::newBean('HintAccountsets'))
            ->where()
            ->equals('assigned_user_id', $userId);

        $rows = $query->execute();

        return array_column($rows, 'id');
    }

    /**
     * Get target ids by user id
     *
     * @param string $userId
     * @return array
     * @throws \SugarQueryException
     */
    protected function getTargetIdsByUserId($userId)
    {
        $query = new \SugarQuery();
        $query->select(['id']);
        $query->from(\BeanFactory::newBean('HintNotificationTargets'))
            ->where()
            ->equals('assigned_user_id', $userId);

        $rows = $query->execute();

        return array_column($rows, 'id');
    }
}
