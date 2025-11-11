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

use Sugarcrm\Sugarcrm\ProcessManager\Registry;

use RRule\RRule;
use RRule\RSet;

trait RecurringCalendarEventTrait
{

    /**
     * Checks wether an event is recurring or not
     * @return bool
     *
     * @throws SugarException
     */
    public function isEventRecurring(): bool
    {
        $logger = LoggerManager::getLogger();
        $isRecurring = (!empty($this->repeat_parent_id) || !empty($this->rset)) && !empty($this->date_start);

        if ($isRecurring) {
            $logger->debug(sprintf('%s/%s is recurring', $this->module_name, $this->id));
        } else {
            $logger->debug(sprintf('%s/%s is not recurring', $this->module_name, $this->id));
        }

        return $isRecurring;
    }

    /**
     * Save a list of recurring events
     *
     * @return array events saved
     *
     * @throws SugarException
     */
    public function saveRecurringEvents(): array
    {
        global $log;

        if (!$this->isEventRecurring()) {
            $logmsg = 'SaveRecurringEvents() : Event is not a Recurring Event';
            $log->error($logmsg);

            throw new SugarException('LBL_CALENDAR_EVENT_NOT_A_RECURRING_EVENT', [$this->object_name]);
        }

        if (!empty($this->repeat_parent_id)) {
            $logmsg = 'SaveRecurringEvents() : Event received is not the Parent Occcurrence';
            $log->error($logmsg);

            throw new SugarException('LBL_CALENDAR_EVENT_IS_NOT_A_PARENT_OCCURRENCE', [$this->object_name]);
        }

        $calendarUtils = CalendarEventsUtils::getInstance();

        $occurrences = $this->getOccurrencesArray();

        $limit = $calendarUtils->getRecurringLimit();

        if (safeCount($occurrences) > $limit) {
            $logMessage = sprintf(
                'Calendar Events (%d) exceed Event Limit: (%d)',
                safeCount($occurrences),
                $limit
            );

            $log->warning($logMessage);
        }

        // Turn off The Cache Updates while deleting the multiple recurrences.
        // The current Cache Enabled status is returned so it can be appropriately
        // restored when all the recurrences have been deleted.
        $cacheEnabled = vCal::setCacheUpdateEnabled(false);
        $this->markRepeatDeleted();

        // Restore the Cache Enabled status to its previous state
        vCal::setCacheUpdateEnabled($cacheEnabled);

        return $this->saveRecurring($occurrences);
    }

    /**
     * Delete recurring activities and their invitee relationships.
     *
     * {@link SugarBean::mark_deleted()} is not used because it runs slowly. The before_delete and after_delete logic
     * hooks are triggered, but the before_relationship_delete and after_relationship_delete logic hooks are not
     * triggered.
     */
    public function markRepeatDeleted()
    {
        global $current_user, $timedate;

        $db = DBManagerFactory::getInstance();
        $modified_user_id = empty($current_user) ? 1 : $current_user->id;
        $date_modified = $timedate->nowDb();
        $lower_name = strtolower($this->object_name);

        $sq = new SugarQuery();
        $sq->select(['id']);
        $sq->from($this);
        $sq->where()->equals('repeat_parent_id', $this->id);
        $rows = $sq->execute();

        foreach ($rows as $row) {
            $bean = BeanFactory::retrieveBean($this->module_name, $row['id']);

            if ($bean) {
                $bean->call_custom_logic('before_delete', ['id' => $bean->id]);

                // Delete the occurrence.
                $db->query("UPDATE {$bean->table_name} SET deleted = 1, date_modified = "
                    . $db->convert($db->quoted($date_modified), 'datetime')
                    . ', modified_user_id = ' . $db->quoted($modified_user_id)
                    . ' WHERE id = ' . $db->quoted($row['id']));

                // Remove the contacts invitees.
                $db->query("UPDATE {$bean->rel_contacts_table} SET deleted = 1, date_modified = "
                    . $db->convert($db->quoted($date_modified), 'datetime')
                    . " WHERE {$lower_name}_id = " . $db->quoted($row['id']));

                if ($bean->load_relationship('contacts')) {
                    $bean->contacts->resetLoaded();
                }

                // Remove the leads invitees.
                $db->query("UPDATE {$bean->rel_leads_table} SET deleted = 1, date_modified = "
                    . $db->convert($db->quoted($date_modified), 'datetime')
                    . " WHERE {$lower_name}_id = " . $db->quoted($row['id']));

                if ($bean->load_relationship('leads')) {
                    $bean->leads->resetLoaded();
                }

                // Remove the users invitees.
                $db->query("UPDATE {$bean->rel_users_table} SET deleted = 1, date_modified = "
                    . $db->convert($db->quoted($date_modified), 'datetime')
                    . " WHERE {$lower_name}_id = " . $db->quoted($row['id']));

                if ($bean->load_relationship('users')) {
                    $bean->users->resetLoaded();
                }

                $bean->call_custom_logic('after_delete', ['id' => $bean->id]);
            }
        }

        CalendarEventsUtils::getInstance()->rebuildFreeBusyCache($GLOBALS['current_user']);
    }

    /**
     * Reload all invitees relationships.
     *
     * This guarantees that any changes to the parent event's invitees will be replicated to all children. This is of
     * particular importance to the users relationship (and users_arr), which must be up-to-date during the child bean's
     * save operation because of the auto-accept logic that exists in {@link CalendarEvent::save()}}.
     *
     * @param array $repeatDateTimeArray
     *
     * @return array events saved
     */
    protected function saveRecurring(array $repeatDateTimeArray): array
    {
        if ($this->load_relationship('contacts')) {
            $this->contacts->resetLoaded();
            $this->contacts_arr = $this->contacts->get();
        }

        if ($this->load_relationship('leads')) {
            $this->leads->resetLoaded();
            $this->leads_arr = $this->leads->get();
        }

        if ($this->load_relationship('users')) {
            $this->users->resetLoaded();
            $this->users_arr = $this->users->get();
        }

        /*--- Parent Bean previously Created - Remove it from the List ---*/
        if (safeCount($repeatDateTimeArray) > 0) {
            unset($repeatDateTimeArray[0]);
        }

        return $this->createRecurringEvents($repeatDateTimeArray);
    }

    /**
     * Save repeat activities.
     *
     * @param array $timeArray Array of start datetimes for each occurrence in the series.
     *
     * @return array
     */
    public function createRecurringEvents(array $timeArray): array
    {
        global $timedate, $current_user;

        set_time_limit(0); // Required to prevent inadvertent timeouts for large recurring series

        $contacts = $this->get_linked_beans('contacts', 'Contact');
        $leads = $this->get_linked_beans('leads', 'Lead');
        $users = $this->get_linked_beans('users', 'User');

        Activity::disable();

        $this->load_relationship('tag_link');
        $parentTagBeans = $this->tag_link->getBeans();

        $arr = [];
        $i = 0;
        $clone = clone $this;

        // $clone is a new bean being created - so throw away the cloned fetched_row attribute that incorrectly makes it
        // look like an existing bean.
        $clone->fetched_row = [];

        $datetimeFormat = $timedate->get_date_time_format();
        $timezone = $current_user->getPreference('timezone');
        $timezone = $timezone ? $timezone : 'UTC';

        $userTimezone = new DateTimeZone($timezone);

        foreach ($timeArray as $dateStart) {
            $date = SugarDateTime::createFromFormat($datetimeFormat, $dateStart);
            $date = $date->get("+{$this->duration_hours} Hours")->get("+{$this->duration_minutes} Minutes");
            $dateEnd = $date->setTimezone($userTimezone)->format($datetimeFormat);

            $startDate = SugarDateTime::createFromFormat($datetimeFormat, $dateStart);
            $dateStart = $startDate->setTimezone($userTimezone)->format($datetimeFormat);

            $clone->id = create_guid();
            $clone->new_with_id = true;
            $clone->date_start = $dateStart;
            $clone->date_end = $dateEnd;
            $clone->recurring_source = 'Sugar';
            $clone->repeat_parent_id = $this->id;
            $clone->update_vcal = false;
            $clone->send_invites = false;
            $clone->rset = '';

            // make sure any store relationship info is not saved
            $clone->rel_fields_before_value = [];
            // Before calling save, we need to clear out any existing registered AWF
            // triggered start events so they can continue to trigger.
            Registry\Registry::getInstance()->drop('triggered_starts');
            $clone->save(false);

            if ($clone->id) {
                if ($clone->load_relationship('tag_link')) {
                    $this->reconcileTags($parentTagBeans, $clone);
                }

                if ($clone->load_relationship('contacts')) {
                    $clone->contacts->add($contacts);
                }

                if ($clone->load_relationship('leads')) {
                    $clone->leads->add($leads);
                }

                if ($clone->load_relationship('users')) {
                    // We want to preserve user's accept status for the event.
                    foreach ($users as $user) {
                        $additionalFields = [];

                        if (isset($this->users->rows[$user->id]) &&
                            isset($this->users->rows[$user->id]['accept_status'])) {
                            $additionalFields['accept_status'] = $this->users->rows[$user->id]['accept_status'];
                        }

                        $clone->users->add($user, $additionalFields);
                    }
                }

                // I have absolutely no idea what 44 means
                // this check has been introduced in https://github.com/sugarcrm/Mango/pull/33264
                if ($i < 44) {
                    $clone->date_start = $dateStart;
                    $clone->date_end = $dateEnd;
                    $arr[] = array_merge(['id' => $clone->id], $clone->getTimeData());
                }

                $i++;
            }
        }

        Activity::restoreToPreviousState();
        CalendarEventsUtils::getInstance()->rebuildFreeBusyCache($GLOBALS['current_user']);

        return $arr;
    }

    /**
     * Reconcile Tags on Child Bean to Match Parent
     *
     * @param array Tag Beans on the Parent Calendar Event
     * @param SugarBean Child Calendar Event Bean
     * @param array Tag Beans currently existing on Child (optional - defaults to empty array)
     */
    public function reconcileTags(array $parentTagBeans, SugarBean $childBean, $childTagBeans = [])
    {
        $sf = SugarFieldHandler::getSugarField('tag');
        $parentTags = $sf->getOriginalTags($parentTagBeans);
        $childTags = $sf->getOriginalTags($childTagBeans);
        [$addTags, $removeTags] = $sf->getChangedValues($childTags, $parentTags);

        // Handle removal of tags
        $sf->removeTagsFromBean($childBean, $childTagBeans, 'tag_link', $removeTags);

        // Handle addition of new tags
        $sf->addTagsToBean($childBean, $parentTagBeans, 'tag_link', $addTags);
    }

    /**
     * Set Start Datetime and End Datetime for an Event
     *
     * @param SugarDateTime $userDateTime in Database Format (UTC)
     */
    public function setStartAndEndDateTime(SugarDateTime $dateStart)
    {
        $dtm = clone $dateStart;
        $this->duration_hours = empty($this->duration_hours) ? 0 : intval($this->duration_hours);
        $this->duration_minutes = empty($this->duration_minutes) ? 0 : intval($this->duration_minutes);
        $this->date_start = $dtm->asDb();

        if ($this->duration_hours > 0) {
            $dtm->modify("+{$this->duration_hours} hours");
        }

        if ($this->duration_minutes > 0) {
            $dtm->modify("+{$this->duration_minutes} mins");
        }

        $this->date_end = $dtm->asDb();

        if (!$this->isEventRecurring()) {
            $this->recurrence_id = '';
        } elseif (!$this->recurrence_id) {
            $this->recurrence_id = $this->date_start;
        }
    }

    /**
     * Update an invitee's accept status for a particular event. Update all future events in the series if the event is
     * recurring.
     *
     * Future events are those that have a status that is neither "Held" nor "Not Held".
     *
     * @param SugarBean $invitee
     * @param string $status
     * @param array $options See {@link BeanFactory::retrieveBean}.
     *
     * @return bool True if at least one accept status was updated.
     * @throws SugarException
     */
    public function updateAcceptStatusForInvitee(
        SugarBean $invitee,
        $status = 'accept',
        $options = []
    ): bool {
        global $log;

        $changeWasMade = false;

        if (in_array($this->status, ['Held', 'Not Held'])) {
            $log->debug(
                sprintf(
                    'Do not update the %s/%s accept status for the parent event %s/%s when the event status is %s',
                    $invitee->module_name,
                    $invitee->id,
                    $this->module_name,
                    $this->id,
                    $this->status
                )
            );
        } else {
            $log->debug(
                sprintf(
                    'Set %s/%s accept status to %s for %s/%s',
                    $invitee->module_name,
                    $invitee->id,
                    $status,
                    $this->module_name,
                    $this->id
                )
            );

            $this->update_vcal = false;
            $this->set_accept_status($invitee, $status);

            $changeWasMade = true;
        }

        if ($this->isEventRecurring()) {
            /**
             * Updates the invitee's accept status for one occurrence in the series.
             *
             * @param array $row The child record to update. Only the ID is used.
             */
            $callback = function (array $row) use (
                $invitee,
                $status,
                $options,
                &$changeWasMade
            ) {
                $child = BeanFactory::retrieveBean($this->module_name, $row['id'], $options);

                if ($child) {
                    $GLOBALS['log']->debug(sprintf(
                        'Set %s/%s accept status to %s for %s/%s',
                        $invitee->module_name,
                        $invitee->id,
                        $status,
                        $child->module_name,
                        $child->id
                    ));

                    $child->update_vcal = false;
                    $child->set_accept_status($invitee, $status);

                    $changeWasMade = true;
                } else {
                    $GLOBALS['log']->error("Could not set acceptance status for {$this->module_name}/{$row['id']}");
                }
            };

            $query = $this->getChildrenQuery();
            $log->debug('Only update occurrences that have not been held or canceled');

            $query->where()
                ->notEquals('status', 'Held')
                ->notEquals('status', 'Not Held');
            $this->repeatAction($query, $callback);
        }

        if ($changeWasMade) {
            if ($invitee instanceof User) {
                $log->debug(sprintf('Update vCal cache for %s/%s', $invitee->module_name, $invitee->id));
                vCal::cache_sugar_vcal($invitee);
            }
        }

        return $changeWasMade;
    }

    /**
     * Returns a SugarQuery object that can be used to fetch all of the child events in a recurring series.
     *
     * @return SugarQuery Modify the object to restrict the result set based on additional conditions.
     * @throws SugarQueryException
     */
    protected function getChildrenQuery(): SugarQuery
    {
        global $log;

        $log->debug(sprintf(
            'Building a query to retrieve the IDs for %s records where the repeat_parent_id is %s',
            $this->module_name,
            $this->id
        ));

        $query = new SugarQuery();
        $query->select(['id']);
        $query->from($this);
        $query->where()->equals('repeat_parent_id', $this->id);
        $query->orderBy('date_start', 'ASC');

        return $query;
    }

    /**
     * Repeat the same action for each record returned by a query. This is useful for repeating an action for each child
     * record in a series.
     *
     * Retrieves, from the database, a max of 200 records at a time upon which to perform the action. This is done to
     * reduce the memory footprint in the event that too many records would be loaded into memory.
     *
     * @param SugarQuery $query The SugarQuery object to use to retrieve the records.
     * @param Closure $callback The function to call for each child record. The database row -- as an array -- is
     * passed to the callback.
     */
    protected function repeatAction(SugarQuery $query, Closure $callback)
    {
        global $log;

        $limit = 200;
        $offset = 0;

        do {
            $log->debug(sprintf('Retrieving the next %d records beginning at %d', $limit, $offset));

            $query->limit($limit)->offset($offset);
            $rows = $query->execute();
            $rowCount = safeCount($rows);

            $log->debug(sprintf('Repeating the action on %d events', $rowCount));

            $rows = is_array($rows) ? $rows : [];
            array_walk($rows, $callback);

            $offset += $rowCount;
        } while ($rowCount === $limit);

        $log->debug(sprintf(
            'Finished repeating because the row count %d does not equal the limit %d',
            $rowCount,
            $limit
        ));
    }

    /**
     * Get array of repeat data
     *
     * @return array
     */
    public function getRepeatData(bool $editAllRecurrences = false, bool $dateStart = false): array
    {
        global $timedate;

        if (!empty($this->repeat_parent_id) || (!empty($this->repeat_type) && empty($editAllRecurrences))) {
            if (!empty($this->repeat_parent_id)) {
                $repeatParentId = $this->repeat_parent_id;
            } else {
                $repeatParentId = $this->id;
            }

            return ['repeat_parent_id' => $repeatParentId];
        }

        $arr = [];

        if (!empty($this->repeat_type)) {
            $arr = [
                'repeat_type' => $this->repeat_type,
                'repeat_interval' => $this->repeat_interval,
                'repeat_dow' => $this->repeat_dow,
                'repeat_until' => $this->repeat_until,
                'repeat_count' => $this->repeat_count,
            ];
        }

        if (empty($dateStart)) {
            $dateStart = $this->date_start;
        }

        $date = SugarDateTime::createFromFormat($timedate->get_date_time_format(), $dateStart);

        if (empty($date)) {
            $date = $timedate->getNow(true);
        }

        $arr = array_merge($arr, [
            'current_dow' => $date->format('w'),
            'default_repeat_until' => $date->get('+1 Month')->format($timedate->get_date_format()),
        ]);

        return $arr;
    }

    /**
     * Check if event has repeat children and pass repeat_parent over to the 2nd event in sequence
     *
     * @param string $beanId
     */
    public function correctRecurrences(string $beanId)
    {
        global $db, $timedate;

        if (!$beanId || trim($beanId) == '') {
            return;
        }

        $occurrences = $this->getOccurrences('id', '', [], '1', '', 'date_start');

        $dateModified = $timedate->nowDb();
        $newParentId = false;

        foreach ($occurrences as $occurrence) {
            $newParentId = $occurrence['id'];

            $params = [
                'repeat_parent_id' => null,
                'recurring_source' => null,
                'date_modified' => $dateModified,
            ];

            $db->updateParams(
                $this->table_name,
                $this->field_defs,
                $params,
                ['id' => $newParentId]
            );
        }

        if ($newParentId) {
            $params = [
                'repeat_parent_id' => $newParentId,
                'date_modified' => $dateModified,
            ];

            $db->updateParams(
                $this->table_name,
                $this->field_defs,
                $params,
                ['repeat_parent_id' => $beanId]
            );
        }
    }

    /**
     * Get occurrence ID by start date
     *
     * @param string $occurrenceStartDate
     *
     * @return string
     */
    public function getOccurrenceIdFromStartDate(string $occurrenceStartDate): string
    {
        $occurrenceId = '';

        if (!$this->isEventRecurring()) {
            return $occurrenceId;
        }

        $td = new SugarDateTime();
        $td->modify($occurrenceStartDate);

        $query = new SugarQuery();
        $query->select(['id']);
        $query->from($this);
        $query->where()->equals('original_start_date', $td->asDb());
        $query->where()->equals('repeat_parent_id', $this->id);
        $query->where()->equals('deleted', 0);

        $occurrences = $query->execute();

        foreach ($occurrences as $occurrence) {
            $occurrenceId = $occurrence['id'];
        }

        return $occurrenceId;
    }

    /**
     * Retrieve a list of occurrences
     *
     * @param array|string $select
     * @param string $eventType
     * @param array $occurrencesStartDates
     * @param string $limit
     * @param string $offset
     * @param string $orderBy
     * @param string $orderByDirection
     *
     * @return array
     */
    public function getOccurrences(
        $select = '*',
        string $eventType = '',
        array $occurrencesStartDates = [],
        string $limit = '',
        string $offset = '',
        string $orderBy = '',
        string $orderByDirection = 'ASC'
    ): array {
        $occurrences = [];
        if (!$this->isEventRecurring()) {
            return $occurrences;
        }

        $query = new SugarQuery();
        $query->select($select);
        $query->from($this);
        $query->where()->equals('repeat_parent_id', $this->id);
        $query->where()->equals('deleted', 0);

        if (!empty($eventType)) {
            $query->where()->equals('event_type', $eventType);
        }

        if (!empty($occurrencesStartDates)) {
            $query->where()->in('original_start_date', $occurrencesStartDates);
        }

        if (!empty($limit)) {
            $query->limit($limit);
        }

        if (!empty($offset)) {
            $query->offset($offset);
        }

        if (!empty($orderBy) && !empty($orderByDirection)) {
            $query->orderBy($orderBy, $orderByDirection);
        }

        $occurrences = $query->execute();

        return $occurrences;
    }

    /**
     * Returns the array of occurrences from the Rset created
     *
     * @return array occurrences
     */
    public function getOccurrencesArray(): array
    {
        $rset = !empty(json_decode($this->rset, true)) ? json_decode($this->rset, true) : $this->rset;

        if (!$rset) {
            $rset = [];
        }

        $rrule= isset($rset['rrule']) ? $rset['rrule'] : '';

        $occurrences = CalendarEventsUtils::getInstance()->createRsetAndGetOccurrences($rrule);

        return $occurrences;
    }

    /**
     * Update the Master Rset Exdate value with the originalStartDate of the event that becomes an exception
     * @param string $originalStartDate
     */
    public function updateRsetExDate(string $originalStartDate)
    {
        if (!empty($this->rset)) {
            $rsetArray = json_decode($this->rset, true);

            if (!$rsetArray) {
                $rsetArray = [];
            }

            if (!array_key_exists('exdate', $rsetArray)) {
                $rsetArray['exdate'] = [];
            }

            if (!in_array($originalStartDate, $rsetArray['exdate'])) {
                $rsetArray['exdate'][] = $originalStartDate;
            }

            $updatedRset = json_encode($rsetArray);

            $this->rset = $updatedRset;
            $this->save();
        }
    }

    /**
     * Gets the Master Rset Exdate value
     *
     * @return array exdate
     */
    public function getRsetExDate(): array
    {
        if (!empty($this->rset)) {
            $rsetArray = json_decode($this->rset, true);

            if (!$rsetArray) {
                $rsetArray = [];
            }

            if (!array_key_exists('exdate', $rsetArray)) {
                $rsetArray['exdate'] = [];
            }

            return $rsetArray['exdate'];
        }

        return [];
    }

    /**
     * Gets the Master Rset Rrule value
     *
     * @return string rrule
     */
    public function getRsetRrule(): string
    {
        if (!empty($this->rset)) {
            $rsetArray = json_decode($this->rset, true);

            if (!$rsetArray) {
                $rsetArray = [];
            }

            if (!array_key_exists('rrule', $rsetArray)) {
                $rsetArray['rrule'] = '';
            }

            return $rsetArray['rrule'];
        }

        return '';
    }

    /**
     * Gets the Master Rset sugarSupportedRrule value
     *
     * @return boolean|string sugarSupportedRrule
     */
    public function getRsetSugarSupportedRrule()
    {
        if (!empty($this->rset)) {
            $rsetArray = json_decode($this->rset, true);

            if (!$rsetArray) {
                $rsetArray = [];
            }

            if (!array_key_exists('sugarSupportedRrule', $rsetArray)) {
                $rsetArray['sugarSupportedRrule'] = true;
            }

            return $rsetArray['sugarSupportedRrule'];
        }

        return '';
    }
}
