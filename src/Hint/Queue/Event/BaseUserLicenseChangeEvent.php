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

class BaseUserLicenseChangeEvent extends QueueEvent
{
    /**
     * Get processor type for this event
     *
     * @return string
     */
    public function getEventType(): string
    {
        return EventTypes::MIXED;
    }

    /**
     * Converts event to format compatible with event queue
     *
     * @param string $accountSetEvent
     * @param string $targetEvent
     * @return array
     */
    public function baseToQueueRows($accountSetEvent, $targetEvent): array
    {
        $rows = [];

        $ids = $this->getAccountsetIdsByUserId($this->data['userId']);
        if ($ids) {
            $rows[] = [
                'type' => $accountSetEvent,
                'data' => [
                    'accountsetIds' => $ids,
                ],
            ];
        }

        $ids = $this->getTargetIdsByUserId($this->data['userId']);
        if ($ids) {
            $rows[] = [
                'type' => $targetEvent,
                'data' => [
                    'targetIds' => $ids,
                ],
            ];
        }

        return $rows;
    }

    /**
     * Get Hint Accountset IDs by user id
     *
     * @param $userId
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
     * @param $userId
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
