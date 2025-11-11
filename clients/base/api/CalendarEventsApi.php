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

use Sugarcrm\Sugarcrm\SugarConnect\Configuration\Configuration as SugarConnectConfiguration;

class CalendarEventsApi extends ModuleApi
{
    /**
     * @var CalendarEvents
     */
    protected $calendarEvents;

    /**
     * {@inheritDoc}
     */
    public function registerApiRest()
    {
        // Return any API definition that exists for this class
        return [];
    }

    /**
     * Tailor the specification (e.g. path) for the specified module and merge in the API specification passed in
     * @param string $module
     * @param array $childApi defaults to empty array
     * @return array
     */
    protected function getRestApi($module, $childApi = [])
    {
        $calendarEventsApi = [
            'create' => [
                'reqType' => 'POST',
                'path' => [$module],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a single event record or a series of event records of the specified type',
                'longHelp' => 'include/api/help/calendar_events_record_create_help.html',
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => [$module, '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateCalendarEvent',
                'shortHelp' => 'This method updates a single event record or a series of event records of the specified type',
                'longHelp' => 'include/api/help/calendar_events_record_update_help.html',
            ],
            'updateOccurrence' => [
                'reqType' => 'POST',
                'path' => [$module, 'updateOccurrence'],
                'pathVars' => ['module', ''],
                'method' => 'updateOccurrenceEvent',
                'shortHelp' => 'This method updates a single event record of the specified type',
                'longHelp' => 'include/api/help/calendar_events_record_update_occurrence_help.html',
                'minVersion' => '11.23',
            ],
            'retrieveOccurrences' => [
                'reqType' => 'POST',
                'path' => [$module, 'retrieveOccurrences'],
                'pathVars' => ['module', ''],
                'method' => 'retrieveOccurrencesEvents',
                'shortHelp' => 'This method retrieves some of all occurrences of a certain type',
                'longHelp' => 'include/api/help/calendar_events_retrieve_occurrences_help.html',
                'minVersion' => '11.23',
            ],
            'delete' => [
                'reqType' => 'DELETE',
                'path' => [$module, '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'deleteCalendarEvent',
                'shortHelp' => 'This method deletes a single event record or a series of event records of the specified type',
                'longHelp' => 'include/api/help/calendar_events_record_delete_help.html',
            ],
        ];

        return array_merge($calendarEventsApi, $childApi);
    }

    /**
     * Update a certain occurrence of a master event
     * @param ServiceBase $api
     * @param array $args API arguments
     *
     * @return array
     */
    public function updateOccurrenceEvent(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'originalStartDate', 'masterRecordId']);

        $masterEvent = BeanFactory::retrieveBean($args['module'], $args['masterRecordId']);

        if (!$masterEvent || !$masterEvent->isEventRecurring()) {
            throw new SugarApiExceptionMissingParameter('Master Event not found!');
        }
        $occurrenceId = $masterEvent->getOccurrenceIdFromStartDate($args['originalStartDate']);

        if (empty($occurrenceId)) {
            throw new SugarApiExceptionMissingParameter('Occurrence not found!');
        }

        $args['record'] = $occurrenceId;

        return $this->updateCalendarEvent($api, $args);
    }

    /**
     * Get a list of occurrences
     * @param ServiceBase $api
     * @param array $args API arguments
     *
     * @return array
     */
    public function retrieveOccurrencesEvents(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['module', 'masterRecordId']);

        $masterEvent = BeanFactory::retrieveBean($args['module'], $args['masterRecordId']);

        if (!$masterEvent || !$masterEvent->isEventRecurring()) {
            return [];
        }

        $limit = array_key_exists('limit', $args) ? $args['limit'] : '';
        $offset = array_key_exists('offset', $args) ? $args['offset'] : '';
        $eventType = array_key_exists('eventType', $args) ? $args['eventType'] : '';
        $orderBy = array_key_exists('orderBy', $args) ? $args['orderBy'] : '';
        $orderByDirection = array_key_exists('orderByDirection', $args) ? $args['orderByDirection'] : 'ASC';
        $occurrencesStartDates = array_key_exists('occurrences', $args) ? $args['occurrences'] : [];

        $occurrences = $masterEvent->getOccurrences(
            '*',
            $eventType,
            $occurrencesStartDates,
            $limit,
            $offset,
            $orderBy,
            $orderByDirection
        );

        return $occurrences;
    }
    /**
     * Create either a single event record or a set of recurring events if record is a recurring event
     * @param ServiceBase $api
     * @param array $args API arguments
     * @param array $additionalProperties Additional properties to be set on the bean
     * @return SugarBean
     */
    public function createBean(ServiceBase $api, array $args, array $additionalProperties = [])
    {
        $this->requireArgs($args, ['module']);

        if (empty($args['date_start'])) {
            throw new SugarApiExceptionMissingParameter('Missing parameter: date_start');
        }

        $args = $this->initializeArgs($args, null)['args'];

        $this->adjustStartDate($args); // adjust start date as necessary if this is a recurring event

        $calendarUtils = CalendarEventsUtils::getInstance();
        $calendarUtils->setOldAssignedUser($args['module'], null);

        $bean = parent::createBean($api, $args, $additionalProperties);
        if (!empty($bean->id)) {
            if ($this->shouldAutoInviteParent($bean, $args)) {
                $bean->inviteParent($args['parent_type'], $args['parent_id']);
            }

            if ($bean->isEventRecurring()) {
                $this->generateRecurringCalendarEvents($bean);
            } else {
                $calendarUtils->rebuildFreeBusyCache($GLOBALS['current_user']);
            }
        }

        return $bean;
    }

    /**
     * Updates either a single event record or a set of recurring events based on all_recurrences flag
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateCalendarEvent(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $calendarUtils = CalendarEventsUtils::getInstance();
        $calendarUtils->setOldAssignedUser($args['module'], $args['record']);

        $api->action = 'view';
        /** @var Call|Meeting $bean */
        $bean = $this->loadBean($api, $args, 'view');
        $argsAndProperties = $this->initializeArgs($args, $bean);
        $args = $argsAndProperties['args'];
        $beanProperties = $argsAndProperties['beanProperties'];

        foreach ($beanProperties as $beanProperty => $beanPropertyValue) {
            $bean->{$beanProperty} = $beanPropertyValue;
        }

        if ($this->shouldAutoInviteParent($bean, $args)) {
            $bean->inviteParent($args['parent_type'], $args['parent_id']);
        }

        $updateResult = [];

        if ($bean->isEventRecurring()) {
            if ((isset($args['all_recurrences']) && ($args['all_recurrences'] === true || $args['all_recurrences'] === 'true')) ||
                (isset($args['rset']) && isset($args['external_system']) &&
                ($args['external_system'] === true || $args['external_system'] === 'true'))) {
                if ($bean->event_type !== 'master') {
                    throw new SugarApiExceptionInvalidParameter('An occurence of an event should never contain a rset or update all recurrences');
                }

                $updateResult = $this->updateRecurringCalendarEvent($bean, $api, $args);
            } else {
                // when updating a single occurrence of a recurring meeting without the
                // `all_recurrences` flag, no updates to recurrence fields are allowed
                $updateResult = $this->updateRecord($api, $this->filterOutRecurrenceFields($args));
                $calendarUtils->rebuildFreeBusyCache($GLOBALS['current_user']);
            }
        } else {
            // adjust start date as necessary if being updated to a recurring event
            $this->adjustStartDate($args);
            $updateResult = $this->updateRecord($api, $args);

            // check if it changed from a non-recurring to recurring & generate events if necessary
            $bean = $this->reloadBean($api, $args);

            if ($bean->isEventRecurring()) {
                $this->generateRecurringCalendarEvents($bean);
            } else {
                $calendarUtils->rebuildFreeBusyCache($GLOBALS['current_user']);
            }
        }
        return $updateResult;
    }

    /**
     * Deletes either a single event record or a set of recurring events based on all_recurrences flag
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function deleteCalendarEvent(ServiceBase $api, array $args)
    {
        if (isset($args['all_recurrences']) && $args['all_recurrences'] === 'true') {
            $result = $this->deleteRecordAndRecurrences($api, $args);
        } else {
            $result = $this->deleteRecord($api, $args);
        }
        return $result;
    }

    /**
     * Creates child events in recurring series
     * @param SugarBean $bean
     */
    public function generateRecurringCalendarEvents(SugarBean $bean)
    {
        $bean->saveRecurringEvents();
    }

    /**
     * Re-generates child events in recurring series
     * @param SugarBean $bean
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter - when updating using the 'all_recurrences' option, the id of the
     *         Parent (root) bean must be provided.
     */
    public function updateRecurringCalendarEvent(SugarBean $bean, ServiceBase $api, array &$args)
    {
        if (!empty($bean->repeat_parent_id) && ($bean->repeat_parent_id !== $bean->id)) {
            throw new SugarApiExceptionInvalidParameter('ERR_CALENDAR_CANNOT_UPDATE_FROM_CHILD');
        }

        $this->adjustStartDate($args); // adjust start date as necessary

        if (isset($args['rset']) && empty($args['rset'])) {
            $args['repeat_type'] = '';
            $args['repeat_count'] = 0;
            $args['repeat_until'] = '';
            $args['repeat_interval'] = 0;
            $args['repeat_dow'] = '';
            $args['repeat_selector'] = 'None';
            $args['repeat_days'] = '';
            $args['repeat_ordinal'] = '';
            $args['repeat_unit'] = '';
            $args['event_type'] = '';
        }

        $api->action = 'save';
        $this->updateRecord($api, $args);
        // if event is still recurring after update, save recurring events
        if ($bean->isEventRecurring()) {
            $bean->saveRecurringEvents();
        } else {
            // event is not recurring anymore, delete child instances
            $this->deleteRecurrences($bean);
        }

        return $this->getLoadedAndFormattedBean($api, $args);
    }

    /**
     * Deletes the parent and associated child events in a series.
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function deleteRecordAndRecurrences(ServiceBase $api, array $args)
    {
        /** @var Call|Meeting $bean */
        $bean = $this->loadBean($api, $args, 'delete');

        if (!empty($bean->repeat_parent_id)) {
            $parentArgs = array_merge(
                $args,
                ['record' => $bean->repeat_parent_id]
            );

            $bean = $this->loadBean($api, $parentArgs, 'delete');
        }

        // Turn off The Cache Updates while deleting the multiple recurrences.
        // The current Cache Enabled status is returned so it can be appropriately
        // restored when all the recurrences have been deleted.
        $cacheEnabled = vCal::setCacheUpdateEnabled(false);
        $this->deleteRecurrences($bean);
        $bean->mark_deleted($bean->id);
        // Restore the Cache Enabled status to its previous state
        vCal::setCacheUpdateEnabled($cacheEnabled);

        CalendarEventsUtils::getInstance()->rebuildFreeBusyCache($GLOBALS['current_user']);

        return ['id' => $bean->id];
    }

    /**
     * Deletes the child recurrences of the given bean
     *
     * @param SugarBean $bean
     */
    public function deleteRecurrences(SugarBean $bean)
    {
        $bean->markRepeatDeleted();
    }

    /**
     * If the event specifies a recurring series, ensure that the series date_start represents
     * the first date in the series.
     *
     * @param array $args
     * @param SugarBean $bean
     * @return array
     */
    protected function initializeArgs(array $args, SugarBean $bean = null)
    {
        $calendarUtils = CalendarEventsUtils::getInstance();
        $beanProperties = [];
        $canChangeRecurrencePattern = true;

        $repeatType = '';
        if (!empty($bean)) {
            $repeatType = empty($bean->repeat_type) ? '' : $bean->repeat_type;

            if (!isset($args['all_recurrences']) &&
                !(!$bean->isEventRecurring() &&
                isset($args['repeat_type']) &&
                !empty($args['repeat_type']) &&
                $args['repeat_type'] !== 'None')) {
                $canChangeRecurrencePattern = false;
            }
        }

        if (isset($args['external_system']) &&
            ($args['external_system'] === true || $args['external_system'] === 'true')) {
            $targetRset = '';
            $usingExistingRset = false;

            // handle old master meetings that were not RFC 5545 compliant
            if ($bean && empty($bean->event_type) && empty($bean->repeat_parent_id)) {
                $bean->sanitizeOccurrences();

                $beanProperties = [
                    'rset' => $bean->rset,
                    'event_type' => $bean->event_type,
                    'original_start_date' => $bean->original_start_date,
                ];
            }

            if (isset($args['rset'])) {
                if (empty($args['rset'])) {
                    $args['repeat_type'] = '';
                } else {
                    $targetRset = $args['rset'];
                }
            } elseif ($bean && isset($bean->rset) && !empty($bean->rset) && $bean->event_type === 'master') {
                $targetRset = $bean->rset;
                $usingExistingRset = true;
            }

            if (isset($targetRset) && !empty($targetRset)) {
                $rSet = json_decode($targetRset, true);

                if ($rSet === null) {
                    throw new SugarApiExceptionInvalidParameter('Invalid rset json data');
                }

                $rrule = isset($rSet['rrule']) ? $rSet['rrule'] : '';
                $newArgs = $calendarUtils->translateRRuleToSugarRecurrence($rrule);
                if (!$usingExistingRset) {

                    $newRset = [
                        'rrule' => $rrule,
                        'exdate' => $rSet['exdate'] ? $rSet['exdate'] : [],
                        'sugarSupportedRrule' => $newArgs['sugarSupportedRrule'],
                    ];
                    $args['rset'] = json_encode($newRset);
                }

                $defaultValues = [
                    'repeat_count' => 0,
                    'repeat_until' => '',
                    'repeat_interval' => 0,
                    'repeat_dow' => '',
                    'repeat_selector' => 'None',
                    'repeat_days' => '',
                    'repeat_ordinal' => '',
                    'repeat_type' => '',
                    'repeat_unit' => '',
                    'date_start' => '',
                ];

                foreach ($defaultValues as $field => $defaultValue) {
                    $args[$field] = $newArgs[$field] ?? $defaultValue;
                }
            }
        } elseif (array_key_exists('repeat_type', $args) &&
            !empty($args['repeat_type']) &&
            $args['repeat_type'] !== 'None' &&
            empty($args['repeat_parent_id']) &&
            $canChangeRecurrencePattern) {
            $exdate = [];
            if ($bean instanceof SugarBean && !isset($args['all_recurrences'])) {
                $exdate = $bean->getRsetExDate();
            }

            $rrule = $calendarUtils->getRruleStringFromParams($args);
            $args['rset'] = json_encode([
                'rrule' => $rrule,
                'exdate' => $exdate,
                'sugarSupportedRrule' => true,
            ]);
        } elseif ($canChangeRecurrencePattern) {
            $args['rset'] = '';
        }

        if (array_key_exists('repeat_type', $args)) {
            $repeatType = empty($args['repeat_type']) ? '' : $args['repeat_type'];
        }

        if (empty($repeatType) || $repeatType === 'None') {
            $args['repeat_count'] = 0;
            $args['repeat_until'] = '';
            $args['repeat_interval'] = 0;
            $args['repeat_dow'] = '';
            $args['repeat_selector'] = 'None';
            $args['repeat_days'] = '';
            $args['repeat_ordinal'] = '';
            $args['repeat_unit'] = '';
        } else {
            $repeatCount = 0;
            if (!empty($bean)) {
                $repeatCount = empty($bean->repeat_count) ? 0 : intval($bean->repeat_count);
                if (!empty($args['repeat_until'])) {
                    $repeatCount = 0;
                }
            }
            if (array_key_exists('repeat_count', $args)) {
                $repeatCount = empty($args['repeat_count']) ? 0 : intval($args['repeat_count']);
            }
            $repeatInterval = 0;
            if (!empty($bean)) {
                $repeatInterval = empty($bean->repeat_interval) ? 0 : intval($bean->repeat_interval);
            }
            if (array_key_exists('repeat_interval', $args)) {
                $repeatInterval = empty($args['repeat_interval']) ? 0 : intval($args['repeat_interval']);
            }
            if (empty($repeatInterval)) {
                $repeatInterval = 1;
            }
            if ($repeatCount > 0) {
                $args['repeat_until'] = '';
            }
            if ($repeatType != 'Monthly' && $repeatType != 'Yearly') {
                $args['repeat_selector'] = 'None';
                $args['repeat_days'] = '';
                $args['repeat_ordinal'] = '';
                $args['repeat_unit'] = '';
            }
            if ($repeatType != 'Weekly') {
                $args['repeat_dow'] = '';
            }
            $args['repeat_count'] = $repeatCount;
            $args['repeat_interval'] = $repeatInterval;
        }

        // Only support the email_addresses argument for Sugar Connect.
        $config = new SugarConnectConfiguration();
        if ($config->isEnabled()) {
            return ['args' => $this->convertInviteeEmailsToIds($args, $bean), 'beanProperties' => $beanProperties];
        }

        return ['args' => $args, 'beanProperties' => $beanProperties];
    }

    /**
     * If the event specifies a recurring series, ensure that the series date_start represents
     * the first date in the series.
     * @param array $args
     */
    protected function adjustStartDate(array &$args)
    {
        if (!empty($args['rset']) && !empty($args['date_start'])) {
            global $timedate;

            $sequence = $this->getRecurringSequence($args);

            if (empty($sequence)) {
                throw new SugarApiExceptionMissingParameter('ERR_CALENDAR_NO_EVENTS_GENERATED');
            }

            $utcTimezone = new DateTimeZone('UTC');
            $firstEventInSeriesKey = 0;

            $startDate = SugarDateTime::createFromFormat(
                $timedate->get_date_time_format(),
                $sequence[$firstEventInSeriesKey],
                $utcTimezone
            );

            $firstEventDate = $startDate->format('Y-m-d' . '\T' . 'H:i:s' . '+00:00');

            $args['date_start'] = $firstEventDate;
        }
    }

    /**
     * Filter out recurrence fields from the API arguments
     *
     * @param array $args
     * @return array
     */
    protected function filterOutRecurrenceFields(array $args)
    {
        $recurrenceFieldBlacklist = [
            'repeat_type',
            'repeat_interval',
            'repeat_dow',
            'repeat_until',
            'repeat_count',
            'repeat_selector',
            'repeat_days',
            'repeat_ordinal',
            'repeat_unit',
        ];
        foreach ($recurrenceFieldBlacklist as $fieldName) {
            unset($args[$fieldName]);
        }
        return $args;
    }

    /**
     * Lazily loads CalendarEvents service
     *
     * @return CalendarEvents
     * @deprecated Will be removed in a future release.
     */
    protected function getCalendarEvents()
    {
        if (!$this->calendarEvents) {
            $this->calendarEvents = new CalendarEvents();
        }

        return $this->calendarEvents;
    }

    /**
     * Determine if parent field record should be automatically added as an
     * invitee on the event.
     *
     * On create, happens if parent field is set and auto_invite_parent is not
     * false. On update, happens if parent field is updated and
     * auto_invite_parent is not false.
     *
     * @param SugarBean $bean
     * @param array $args
     * @return bool
     */
    protected function shouldAutoInviteParent(SugarBean $bean, array $args)
    {
        $isUpdate = isset($args['id']);

        // allow auto invite to be turned off with flag on the request
        if (isset($args['auto_invite_parent']) && $args['auto_invite_parent'] === false) {
            return false;
        }

        // if parent field is empty, nothing to auto-invite
        if (empty($args['parent_type']) || empty($args['parent_id'])) {
            return false;
        }

        // if updating and parent field has not changed, no auto-invite
        if ($isUpdate
            && ($bean->parent_type === $args['parent_type'])
            && ($bean->parent_id === $args['parent_id'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Generate the recurring DateTime sequence for a Recurring Event given the Recurring Parent Bean
     *
     * @param array $args
     * @return array
     */
    protected function getRecurringSequence(array $args)
    {
        $calendarUtils = CalendarEventsUtils::getInstance();
        $rset = json_decode($args['rset'], true);

        if ($rset === null) {
            throw new SugarApiExceptionInvalidParameter('Invalid rset json data');
        }

        $rrule = isset($rset['rrule']) ? $rset['rrule'] : '';

        $occurrences = $calendarUtils->createRsetAndGetOccurrences($rrule);

        return $occurrences;
    }

    /**
     * If the REST API arguments contains `email_addresses`, then its contents
     * are used as the current set of event attendees and all others should be
     * removed.
     *
     *   "email_addresses": {
     *       "create": [
     *          {
     *              "email_address": "abc@foo.com",
     *          },
     *          {
     *              "email_address": "xyz@bar.com",
     *          },
     *       ]
     *   }
     *
     *
     * This invitees list supercedes the users, contacts, and leads links.
     * Existing attendees that are not found in this list are removed.
     *
     * @param array $args The API arguments.
     * @param SugarBean|null $bean The bean that is being saved.
     *
     * @return array
     * @deprecated Will be removed in the next release.
     */
    protected function convertInviteeEmailsToIds(array $args, $bean)
    {
        if (!isset($args['email_addresses']['create'])
            || !is_array($args['email_addresses']['create'])
        ) {
            // Nothing to do.
            return $args;
        }

        // Get the current list of attendees. The result will be an empty array
        // if the event is being created.
        $currentAttendees = $this->getAttendees($bean);

        // Lowercase each of the new attendee email addresses on the request.
        // Duplicates are eliminated.
        $newAttendees = [];

        foreach ($args['email_addresses']['create'] as $attendee) {
            if (!empty($attendee['email_address'])) {
                $lower = strtolower($attendee['email_address']);
                $newAttendees[$lower] = $attendee['email_address'];
            }
        }

        // Mixed mode not allowed. When email_addresses is present, the links
        // should not be.
        if (isset($args['users'])
            || isset($args['contacts'])
            || isset($args['leads'])
        ) {
            throw new SugarApiExceptionInvalidParameter(
                'email_addresses cannot be used together with contacts, leads, or users'
            );
        }

        // Loop through the newAttendee emails on the request and if not
        // currently on the attendeee list, append them to the args `add` list
        // for their specific bean person type.
        foreach ($newAttendees as $lcNewAttendeeEmail => $newAttendeeEmail) {
            if (!isset($currentAttendees[$lcNewAttendeeEmail])) {
                $result = $this->convertEmailAddressToPerson($lcNewAttendeeEmail);

                if (!empty($result)) {
                    $beanType = strtolower($result['bean_module']);
                    $args[$beanType]['add'][] = $result['bean_id'];
                }
            }
        }

        $assignee = empty($bean) || empty($bean->assigned_user_id) ? '' : $bean->assigned_user_id;
        $assignee = empty($args['assigned_user_id']) ? $assignee : $args['assigned_user_id'];

        // Any current attendees not in the new attendees list need to be
        // removed so they should be added to the args `delete` list for their
        // specific bean person type.
        foreach ($currentAttendees as $lcCurrentEmail => $attendee) {
            if ($attendee['bean_module'] == 'Users' && $attendee['bean_id'] === $assignee) {
                // assigned_user should never be deleted
            } elseif (!isset($newAttendees[$lcCurrentEmail])) {
                $beanType = strtolower($attendee['bean_module']);
                $args[$beanType]['delete'][] = $attendee['bean_id'];
            }
        }

        return $args;
    }

    /**
     * Select a matching module and ID for the user, contact or lead having the
     * supplied email address. In order to produce a preferred and predictable
     * selection, the best candidate is determined based on whether the email
     * address is primary together with a module order precendnce of: Users,
     * Contacts, then Leads.
     *
     * @param string $emailAddress The email address to search for.
     *
     * @return array The best match for the email address.
     */
    protected function convertEmailAddressToPerson($emailAddress)
    {
        $personModules = [
            'Users' => 'A',
            'Contacts' => 'B',
            'Leads' => 'C',
        ];

        $addressList = [];
        $sugarEmailAddress = new SugarEmailAddress();
        $beans = $sugarEmailAddress->getBeansByEmailAddress($emailAddress);

        foreach ($beans as $bean) {
            $module = $bean->getModuleName();

            if (isset($personModules[$module]) && !empty($bean->emailAddress->addresses)) {
                foreach ($bean->emailAddress->addresses as $addr) {
                    $pri = ($addr['primary_address'] == 1) ? 'A' : 'B';
                    $mod = $personModules[$module];
                    $addressList[$pri . $mod] = $addr;
                }
            }
        }

        if (empty($addressList)) {
            return [];
        }

        ksort($addressList);
        $top = array_shift($addressList);

        return [
            'bean_module' => $top['bean_module'],
            'bean_id' => $top['bean_id'],
            'email_address' => $top['email_address'],
        ];
    }

    /**
     * Get the current list of attendees for the supplied calendar event.
     *
     * @param SugarBean|null $bean Meeting or Call bean.
     *
     * @return array
     */
    protected function getAttendees($bean)
    {
        if (empty($bean)) {
            return [];
        }

        $attendees = [];

        foreach (['users', 'contacts', 'leads'] as $link) {
            if ($bean->load_relationship($link)) {
                $bean->$link->resetLoaded();
                $bean->$link->load();

                foreach ($bean->$link->rows as $beanId => $row) {
                    $person = BeanFactory::retrieveBean(
                        $bean->$link->getRelatedModuleName(),
                        $beanId,
                        ['disable_row_level_security' => true]
                    );

                    if (!$person) {
                        continue;
                    }

                    $attendee = [
                        'bean_module' => $person->getModuleName(),
                        'bean_id' => $person->id,
                        'email_address' => $person->emailAddress->getPrimaryAddress($person),
                    ];
                    $emailAddress = strtolower($attendee['email_address']);

                    if (!isset($attendees[$emailAddress])) {
                        $attendees[$emailAddress] = $attendee;
                    }
                }
            }
        }

        return $attendees;
    }
}
