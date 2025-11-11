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

use Sugarcrm\Sugarcrm\Hint\Logger\Logger;

class UserLicensedEvent extends BaseUserLicenseChangeEvent
{
    /**
     * {@inheritDoc}
     */
    public function toQueueRows(): array
    {
        $logger = new Logger();

        $rows = [];

        return $rows;

        // this code below is close, but not exactly right yet.  In the interests of getting a
        // viable MLP, it is being commented out while a real fix can be developed
        //        $query = new \SugarQuery();
        //        $query->from(\BeanFactory::newBean('HintAccountsets'), ['alias' => 'accountsets']);
        //        $query->join('assigned_user_link', [
        //            'joinType' => 'INNER',
        //            'alias' => 'users'
        //        ]);
        //        $query->join('notification_targets', [
        //            'joinType' => 'left',
        //            'alias' => 'targets'
        //        ]);
        //        $query->join('tag_link', [
        //            'joinType' => 'left',
        //            'alias' => 'tags'
        //        ]);
        //        // we define join aliases before select
        //        $query->select([
        //            // accountset fields
        //            ['id', 'accountset_id'],
        //            'type',
        //            'category',
        //            ['assigned_user_id', 'user_id'],
        //            // related target and tag ids
        //            ['targets.id', 'target_id'],
        //            ['tags.id', 'tag_id'],
        //        ]);
        //        $query->where()->equals('assigned_user_id', $this->data['userId']);
        //
        //        $rows = $query->execute();
        //
        //        $logger->alert('got queue rows' . print_r($rows, true));
        //
        //        // no valid accountsets - stop processing
        //        if (!$rows) {
        //            return [];
        //        }
        ////
        //        // map sql result set (denormalized data) to ACCOUNTSET_ADD event payloads
        //        $data = [];
        //        foreach ($rows as $row) {
        //            $logger->alert('row' . print_r($row, true));
        //
        //            $row = array_change_key_case($row, CASE_LOWER);
        //            $id = $row['accountset_id'];
        //
        //            // prepare pure event payload (accountset + empty arrays of related ids)
        //            if (!isset($data[$id])) {
        //                $data[$id] = array_merge(
        //                    array_intersect_key($row, array_flip(['accountset_id', 'type', 'category', 'user_id'])),
        //                    ['targetIds' => [], 'tagIds' => []]
        //                );
        //            }
        //
        //            // add related ids
        //            if ($row['target_id']) {
        //                $data[$id]['targetIds'][] = $row['target_id'];
        //            }
        //
        //            if ($row['tag_id']) {
        //                $data[$id]['tagIds'][] = $row['tag_id'];
        //            }
        //        }
        //
        //        $logger->alert('event data' . print_r($data, true));

        //        // queue ACCOUNTSET_ADD events with proper payload and user context
        //        foreach ($data as $eventData) {
        //            $eventData = array_merge(['accountsetId' => $eventData['accountset_id']], $eventData);
        //            $context = ['user_id' => $eventData['user_id']];
        //            $this->eventQueue->recordEvent(new AccountsetAddEvent($eventData), $context);
        //        }
        //
        //        return $rows;

        // ------------------------------------------------------------

        //        $targetIds = $this->getTargetIdsByUserId($this->data['userId']);
        //
        //        $ids = $this->getAccountsetIdsByUserId($this->data['userId']);
        //        if ($ids) {
        //            $rows[] = [
        //                'type' => EventTypes::ACCOUNTSET_ADD,
        //                'data' => [
        //                    'accountsetIds' => $ids,
        //                ],
        //            ];
        //
        //
        //        }
        //
        //        if ($ids) {
        //            $rows[] = [
        //                'type' => EventTypes::ACCOUNTSET_ADD_TARGET,
        //                'data' => [
        //                    'targetIds' => $ids
        //                ],
        //            ];
        //        }
        //
        //        return $rows;
        //
        //        $rows =  $this->baseToQueueRows(,
        //            EventTypes::TARGET_ADD);
        //        $logger->alert('got queue rows' . print_r($rows, true));
        //        return $rows;
    }
}
