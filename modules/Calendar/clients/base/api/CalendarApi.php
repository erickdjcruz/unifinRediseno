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

use Sugarcrm\Sugarcrm\Calendar;
use Sugarcrm\Sugarcrm\Util\Uuid;

include_once 'include/workflow/alert_utils.php';

class CalendarApi extends FilterApi
{
    /**
     * @var \LoggerManager|mixed
     */
    public $logger;

    public function registerApiRest()
    {
        return [
            'invitee_search' => [
                'reqType' => 'GET',
                'path' => ['Calendar', 'invitee_search'],
                'pathVars' => ['', ''],
                'method' => 'inviteeSearch',
                'shortHelp' => 'This method searches for people to invite to an event',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_invitee_search_get_help.html',
            ],
            'getEvents' => [
                'reqType' => 'POST',
                'path' => ['Calendar', 'getEvents'],
                'pathVars' => ['module', 'action'],
                'method' => 'getEvents',
                'shortHelp' => 'Retrieve events to show in calendar',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_getevents_post_help.html',
                'minVersion' => '11.14',
            ],
            'updateRecord' => [
                'reqType' => 'POST',
                'path' => ['Calendar', 'updateRecord', '?'],
                'pathVars' => ['calendarModule', 'action', 'recordId'],
                'method' => 'updateRecord',
                'shortHelp' => 'update record',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_updaterecord_post_help.html',
                'minVersion' => '11.14',
            ],
            'listCalendars' => [
                'reqType' => 'POST',
                'path' => ['Calendar', 'calendars'],
                'pathVars' => ['module', 'target'],
                'method' => 'listCalendars',
                'shortHelp' => 'Lists my calendars and others',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_listcalendars_post_help.html',
                'minVersion' => '11.14',
            ],
            'getUsersAndTeams' => [
                'reqType' => 'GET',
                'path' => ['Calendar', 'usersAndTeams'],
                'pathVars' => ['', ''],
                'method' => 'getUsersAndTeams',
                'shortHelp' => 'Get Users And Teams',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_getusersandteams_get_help.html',
                'minVersion' => '11.14',
            ],
            'getCalendarDefs' => [
                'reqType' => 'POST',
                'path' => ['Calendar', 'getCalendarDefs'],
                'pathVars' => ['module', 'action'],
                'method' => 'getCalendarDefs',
                'shortHelp' => 'Get Calendar Definitions.',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_getcalendardefs_post_help.html',
                'minVersion' => '11.14',
            ],
            'getCalendarModules' => [
                'reqType' => 'GET',
                'path' => ['Calendar', 'modules'],
                'pathVars' => ['', ''],
                'method' => 'getCalendarModules',
                'shortHelp' => 'Get modules with Calendar Configurations',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_getcalendarmodules_get_help.html',
                'minVersion' => '11.14',
            ],
            'getICalConfigurationsUID' => [
                'reqType' => 'POST',
                'path' => ['Calendar', 'getICalConfigurationsUID'],
                'pathVars' => ['module', 'action'],
                'method' => 'getICalConfigurationsUID',
                'shortHelp' => 'getICalConfigurationsUID',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_geticalconfigurationsuid_post_help.html',
                'minVersion' => '11.14',
            ],
            'getICalPublishUrl' => [
                'reqType' => 'POST',
                'path' => ['Calendar', 'getICalPublishUrl'],
                'pathVars' => ['module', 'action'],
                'method' => 'getICalPublishUrl',
                'shortHelp' => 'getICalPublishUrl',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_geticalpublishurl_post_help.html',
                'minVersion' => '11.14',
            ],
            'getICalData' => [
                'reqType' => 'GET',
                'path' => ['Calendar', 'getICalData'],
                'pathVars' => ['module', 'action'],
                'method' => 'getICalData',
                'shortHelp' => 'getICalData',
                'longHelp' => 'modules/Calendar/clients/base/api/help/calendar_geticaldata_get_help.html',
                'noLoginRequired' => true,
                'rawReply' => true,
                'minVersion' => '11.14',
            ],
        ];
    }

    /**
     * Set up API
     */
    public function __construct()
    {
        parent::__construct();

        $this->logger = LoggerManager::getLogger();
    }

    /**
     * Run a search for possible invitees to invite to a calendar event.
     *
     * TODO: currently uses legacy code - either replace this backend
     *   implementation with global search when it supports searching across
     *   linked fields like account_name or remove the endpoint altogether.
     *   Either way - remember to update api docs.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     */
    public function inviteeSearch(ServiceBase $api, array $args)
    {
        $api->action = 'list';
        $this->requireArgs($args, ['q', 'module_list', 'search_fields', 'fields']);

        //make legacy search request
        $params = $this->buildSearchParams($args);
        $searchResults = $this->runInviteeQuery($params);

        return $this->transformInvitees($api, $args, $searchResults);
    }

    /**
     * Get events
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getEvents(ServiceBase $api, array $args): array
    {
        $this->requireArgs([
            'startDate',
            'endDate',
            'calendarConfigurations',
        ]);

        $calendarConfigurations = $args['calendarConfigurations'];

        $calendars = $this->prepareCalendars($calendarConfigurations);

        $data = [];

        foreach ($calendars as $calendar) {
            if (isset($calendar['userId']) && $calendar['userId'] != '') {
                self::$current_user = BeanFactory::retrieveBean('Users', $calendar['userId']);
            }

            try {
                $calendarBean = $calendar['calendarBean'];

                $calendarData = [];

                $calendar['params'] = $args;
                $queryManager = new Calendar\QueryManager($calendar);
                $queryManager->setupQuery();
                $rawEventsInDb = $queryManager->executeQuery();

                $calendarData = $this->prepareResult($rawEventsInDb, $calendarBean);

                $data = $this->addEventsToResult($data, $calendarData);
            } catch (Exception $e) {
                $this->logger->error(
                    'Exception while getting events. ' . $e->getMessage() . '. Trace:' . $e->getTraceAsString()
                );

                return [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }
        $users = $this->getUsersInEvents($data);

        return [
            'status' => 'success',
            'message' => 'success api',
            'data' => $data,
            'users' => $users,
            'startDate' => $args['startDate'],
            'endDate' => $args['endDate'],
        ];
    }

    /**
     * Process calendar definition to generate a structure we can rely on
     *
     * @param Array $calendarConfigurations
     * @return Array Dictionaries of calendars
     */
    public function prepareCalendars(array $calendarConfigurations): array
    {
        global $current_user;

        $calendarsPrepared = [];
        foreach ($calendarConfigurations as $calendar) {
            if (!empty($calendar['userId'])) {
                $calendarBean = BeanFactory::retrieveBean('Calendar', $calendar['calendarId']);
                if ($calendarBean instanceof SugarBean) {
                    $calendarBean->setIntervalType();
                    $newCalendar = [
                        'calendarId' => $calendar['calendarId'],
                        'calendarBean' => $calendarBean,
                    ];
                    $newCalendar['userId'] = $calendar['userId'] === 'current_user' ?
                        $current_user->id : $calendar['userId'];
                    $calendarsPrepared[] = $newCalendar;
                }
            } elseif (!empty($calendar['teamId'])) {
                $userSeed = BeanFactory::getBean('Teams', $calendar['teamId']);
                $users = $userSeed->get_team_members();

                $calendarBean = BeanFactory::retrieveBean('Calendar', $calendar['calendarId']);
                foreach ($users as $user) {
                    if ($calendarBean instanceof SugarBean) {
                        $calendarBean->setIntervalType();
                        $newCalendar = [
                            'calendarId' => $calendar['calendarId'],
                            'calendarBean' => $calendarBean,
                        ];
                        $newCalendar['userId'] = $user->id;
                        $calendarsPrepared[] = $newCalendar;
                    }
                }
            }
        }

        $numberOfCalendarsPrepared = safeCount($calendarsPrepared);
        $this->logger->debug("prepareCalendars - prepared {$numberOfCalendarsPrepared} calendars");
        return $calendarsPrepared;
    }

    /**
     * Returns a list with all users which are used in events (assigned users or attending users)
     * @param $data array
     * @return array
     */
    protected function getUsersInEvents(array $data): array
    {
        $users = [];
        $userIds = [];
        foreach ($data as $event) {
            foreach ($event['eventUsers'] as $eventUser) {
                if (!in_array($eventUser, $userIds)) {
                    $userIds[] = $eventUser;
                }
            }
        }
        if (safeCount($userIds) >= 1) {
            $userSeed = BeanFactory::newBean('Users');

            $fields = ['id', 'first_name', 'last_name'];

            $query = new SugarQuery();
            $query->from($userSeed);
            $query->where()->in('id', $userIds);

            $result = $userSeed->fetchFromQuery($query, $fields);

            foreach ($result as $user) {
                $users[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }
        }
        return $users;
    }

    /**
     * Update Record
     *
     * @param ServiceBase $api
     * @param array $args
     * @return bool
     */
    public function updateRecord(ServiceBase $api, array $args): bool
    {
        $start = null;
        $end = null;
        global $timedate, $current_user;

        $calendarBean = BeanFactory::retrieveBean('Calendar', $args['calendarId']);
        $calendarBean->setIntervalType();

        $targetBean = BeanFactory::retrieveBean($args['module'], $args['recordId']);

        if (!$this->hasAccess($targetBean->module_dir, 'save')) {
            return false;
        };

        $userTimeZone = TimeDate::userTimezone($current_user);
        $timezone = new DateTimeZone($userTimeZone);

        $eventStart = date('Y-m-d H:i:sO', strtotime($args['start']));
        $eventStart = SugarDateTime::createFromFormat('Y-m-d H:i:sO', $eventStart, $timezone);

        $eventEnd = date('Y-m-d H:i:sO', strtotime($args['end']));
        $eventEnd = SugarDateTime::createFromFormat('Y-m-d H:i:sO', $eventEnd, $timezone);

        $eventStartFieldType =
            $this->getFieldType($calendarBean->event_start, $calendarBean->calendar_module);

        if ($calendarBean->intervalType == $calendarBean::$INTERVAL_TIME) {
            $eventEndFieldType =
                $this->getFieldType($calendarBean->event_end, $calendarBean->calendar_module);
        }

        if ($eventStartFieldType == 'datetime' || $eventStartFieldType == 'datetimecombo') {
            $start = $timedate->asDb($eventStart);
        } elseif ($eventStartFieldType == 'date') {
            //remove timezone offset to store the correct value in db
            $start = substr($args['start'], 0, 10);
            $eventStart = SugarDateTime::createFromFormat('Y-m-d', $start);

            $end = substr($args['end'], 0, 10);
            $eventEnd = SugarDateTime::createFromFormat('Y-m-d', $end);
        }
        $targetBean->{$calendarBean->event_start} = $start;

        if ($calendarBean->intervalType == $calendarBean::$INTERVAL_DURATION
            || $calendarBean->calendar_module == 'Meetings'
            || $calendarBean->calendar_module == 'Calls'
        ) {
            // hardcode durations because without them, end date is not updated correct
            if ($calendarBean->calendar_module == 'Meetings' || $calendarBean->calendar_module == 'Calls') {
                $calendarBean->duration_hours = 'duration_hours';
                $calendarBean->duration_minutes = 'duration_minutes';
            }

            $fromTime = new DateTime($eventStart);
            $toTime = new DateTime($eventEnd);
            $interval = $fromTime->diff($toTime);

            $minutes = intval($interval->i);
            $hours = intval($interval->h);
            $days = intval($interval->d);

            if (empty($calendarBean->duration_days)) {
                $hours = $hours + ($days * 24);
            } else {
                $targetBean->{$calendarBean->duration_days} = $days;
            }

            if (empty($calendarBean->duration_hours)) {
                $minutes = $minutes + ($hours * 60);
            } else {
                $targetBean->{$calendarBean->duration_hours} = $hours;
            }

            if (!empty($calendarBean->duration_minutes)) {
                $targetBean->{$calendarBean->duration_minutes} = $minutes;
            }
        } else {
            if (isset($eventEndFieldType) &&
                ($eventEndFieldType == 'datetime' || $eventEndFieldType == 'datetimecombo')) {
                $end = $timedate->asDb($eventEnd);
            } elseif ($eventEndFieldType == 'date') {
                $end = date_format($eventEnd, 'Y-m-d');
            }
            $targetBean->{$calendarBean->event_end} = $end;
        }


        if (($calendarBean->calendar_module == 'Calls' || $calendarBean->calendar_module == 'Meetings')
            && isset($args['sendInvites']) && $args['sendInvites']) {
            $usersIds = [];
            $targetBean->load_relationship('users');
            foreach ($targetBean->users->get() as $userId) {
                $usersIds[] = $userId;
            }
            $contactsIds = [];
            $targetBean->load_relationship('contacts');
            foreach ($targetBean->contacts->get() as $contactId) {
                $contactsIds[] = $contactId;
            }
            $leadsIds = [];
            $targetBean->load_relationship('leads');
            foreach ($targetBean->leads->get() as $leadId) {
                $leadsIds[] = $leadId;
            }
            $targetBean->setUserInvitees($usersIds);
            $targetBean->setContactInvitees($contactsIds);
            $targetBean->setLeadInvitees($leadsIds);

            $targetBean->send_invites = true;

            $targetBean->save(true);
        } else {
            if ($calendarBean->calendar_module == 'Opportunities' && Opportunity::usingRevenueLineItems()) {
                if ($calendarBean->event_start == 'service_start_date') {
                    $targetBean->service_start_date_cascade = $targetBean->service_start_date;
                }

                if ($calendarBean->event_end == 'date_closed') {
                    $targetBean->date_closed_cascade = $targetBean->date_closed;
                }
            }
            $targetBean->save();
        }

        $this->logger->info("Calendar update record {$targetBean->module_dir} - {$targetBean->id}");

        return true;
    }

    /**
     * Get field type
     *
     * @param String $field
     * @param String $module
     * @return String
     */
    public function getFieldType(string $fieldName, string $module): string
    {
        $db = \DBManagerFactory::getInstance();
        $bean = \BeanFactory::newBean($module);
        $fieldDef = $bean->field_defs[$fieldName];

        return $db->getFieldType($fieldDef);
    }

    /**
     * Add events from current calendar configuration to the final result
     *
     * @param array $result Data from previous fetches
     * @param array $events
     * @return array
     */
    protected function addEventsToResult(array $result, array $events): array
    {
        $eventIdsAdded = array_column($result, 'id');

        foreach ($events as $event) {
            if (!in_array($event['id'], $eventIdsAdded)) {
                $result[] = $event;
            } else {
                //get the event already added and add the assigned_user_id to eventUsers
                //this way we know the event appears for multiple users and configurations
                for ($i = 0; $i < safeCount($result); $i++) {
                    if ($result[$i]['id'] == $event['id']
                        && !safeInArray($event['eventUsers'][0], $result[$i]['eventUsers'])
                    ) {
                        $result[$i]['eventUsers'][] = $event['eventUsers'][0];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Format the events
     *
     * @param array $data
     * @param SugarBean $calendarBean
     * @return array
     *
     * @throws Exception
     * @throws Error
     */
    protected function prepareResult(array $data, SugarBean $calendarBean): array
    {
        global $timedate;

        $dataFormatted = [];
        $invitees = [];

        if (safeCount($data) > 0 &&
            ($calendarBean->calendar_module == 'Calls' || $calendarBean->calendar_module == 'Meetings')) {
            $invitees = $this->getAllInvitees($data, $calendarBean->calendar_module);
        }

        $eventsTemplates = [
            'event_tooltip' => 'event_tooltip_template',
            'day_event_template' => 'day_event_template',
            'week_event_template' => 'week_event_template',
            'month_event_template' => 'month_event_template',
            'agenda_event_template' => 'agenda_event_template',
            'timeline_event_template' => 'timeline_event_template',
            'schedulermonth_event_template' => 'schedulermonth_event_template',
            'ical_event_template' => 'ical_event_template',
        ];

        // Store related record id for each event
        // Needed when dbl click use a link field (the related id is stored in another table)
        $relIds = [];
        $dblClickAction = explode(':', $calendarBean->dblclick_event);

        $dblClickField = (is_array($dblClickAction) && safeCount($dblClickAction) === 3) ? $dblClickAction[2] : null;
        $dblClickTargetModule = (is_array($dblClickAction) && safeCount($dblClickAction) === 3) ? $dblClickAction[1] : null;

        $seed = BeanFactory::newBean($calendarBean->calendar_module);

        if ($dblClickField && $dblClickTargetModule !== 'self' &&
            array_key_exists('link', $seed->field_defs[$dblClickField])) {
            $linkName = $seed->field_defs[$dblClickField]['link'];
            $seed->load_relationship($linkName);

            $relOpts = [
                'moduleName' => $calendarBean->calendar_module,
                'tableName' => $seed->$linkName->relationship->def['table'],
                'relField' => $dblClickField,
                'lhsKey' => $seed->$linkName->relationship->def['join_key_lhs'],
                'rhsKey' => $seed->$linkName->relationship->def['join_key_rhs'],
                'side' => !empty($seed->$linkName) ? $seed->$linkName->getSide() : '',
            ];

            if (isset($seed->field_defs[$dblClickField]['source']) &&
                $seed->field_defs[$dblClickField]['source'] === 'non-db' &&
                !empty($seed->$linkName->relationship->def['table'])) {
                $relIds = $this->getAllRelatedRecords($relOpts, $data);
            }
        }

        foreach ($data as $rawEvent) {
            if ($this->hasAccess($calendarBean->calendar_module, 'view')) {
                $assignedUser = BeanFactory::retrieveBean('Users', $rawEvent['assigned_user_id']);
                $event = [
                    'calendarId' => $calendarBean->id,
                    'id' => $rawEvent['id'],
                    'eventUsers' => [$rawEvent['assigned_user_id']],
                    'assignedUserName' => $assignedUser->name,
                    'assignedUserId' => $assignedUser->id,
                    'invitees' => $invitees[$rawEvent['id']] ?? [],
                    'name' => $rawEvent[$calendarBean->subject],
                    'title' => $rawEvent[$calendarBean->subject],
                    'color' => $calendarBean->color,
                    'module' => $calendarBean->calendar_module,
                ];

                foreach ($eventsTemplates as $eventName => $beanField) {
                    $event[$eventName] = $this->compileTemplatePrototype($calendarBean, $beanField, $rawEvent);
                }

                if ($dblClickField && $dblClickTargetModule !== 'self' &&
                    isset($seed->field_defs[$dblClickField]['source']) &&
                    $seed->field_defs[$dblClickField]['source'] === 'non-db' &&
                    !empty($seed->$linkName->relationship->def['table']) &&
                    isset($relIds[$rawEvent['id']])) {
                    $event['dbclickRecordId'] = $relIds[$rawEvent['id']];
                } else {
                    $event['dbclickRecordId'] = $rawEvent[$dblClickField];
                }

                //start field
                $startField = $calendarBean->event_start;
                $startFieldType = $this->getFieldType($startField, $calendarBean->calendar_module);

                $start = $timedate->fromDbType($rawEvent[$startField], $startFieldType);

                if ($startFieldType == 'date') {
                    $event['start'] = date_format($start, 'Y-m-d');
                } elseif ($startFieldType == 'datetime') {
                    $event['start'] = date_format($start, 'Y-m-d H:i:sO');
                }

                //calculate end field
                if ($calendarBean->intervalType == $calendarBean::$INTERVAL_TIME) {
                    $endField = $calendarBean->event_end;
                    $endFieldType = $this->getFieldType($endField, $calendarBean->calendar_module);

                    $end = $timedate->fromDbType($rawEvent[$endField], $endFieldType);

                    if ($endFieldType == 'date') {
                        $event['end'] = date_format($end, 'Y-m-d');
                    } elseif ($startFieldType == 'datetime') {
                        $event['end'] = date_format($end, 'Y-m-d H:i:sO');
                    }
                } else {
                    $event['end'] = $this->calculateEventEnd($rawEvent, $calendarBean);
                }

                $dataFormatted[] = $event;
            }
        }

        return $dataFormatted;
    }

    /**
     * Replace placeholder fields in the template with values
     *
     * @param SugarBean $calendarBean
     * @param String|null $beanField The field name template set in the calendar record
     * @param Array $rawEvent Record data from db
     * @return String
     */
    protected function compileTemplatePrototype(SugarBean $calendarBean, $beanField, array $rawEvent): string
    {
        global $dictionary, $timedate;

        $template = $calendarBean->{$beanField};

        $objectName = \BeanFactory::getObjectName($calendarBean->calendar_module);

        $template = trim($template);

        $fieldsToReplace = $this->extractFieldsFromTemplate($template);

        //add relates
        foreach ($fieldsToReplace as $fieldName) {
            $fieldDef = $dictionary[$objectName]['fields'][$fieldName];
            if ($fieldDef['type'] == 'relate') {
                $relatedBean = BeanFactory::retrieveBean($fieldDef['module'], $rawEvent[$fieldDef['id_name']]);
                $rawEvent[$fieldName] = $relatedBean->name;
            }
        }

        $fieldsDictionary = [];
        foreach ($fieldsToReplace as $fieldName) {
            if (array_key_exists($fieldName, $rawEvent)) {
                $rawEventField = trim($rawEvent[$fieldName]); //handle NULL db values
                $fieldDisplay =
                    $this->getFieldDisplayInSugar($calendarBean->calendar_module, $fieldName, $rawEventField);

                $fieldsDictionary['{::' . $fieldName . '::}'] = $fieldDisplay;
            }
        }

        $startField = $calendarBean->event_start;
        $startFieldType = $this->getFieldType($startField, $calendarBean->calendar_module);
        $eventStart = $timedate->fromDbType($rawEvent[$startField], $startFieldType);

        $minutes = $eventStart->format('i');
        if ($minutes == '00') {
            $fieldsDictionary['{::event_timestamp::}'] = $eventStart->format('g A');
        } else {
            $fieldsDictionary['{::event_timestamp::}'] = $eventStart->format('g:i A');
        }

        $compiledTemplate = replace_target_body_items($template, $fieldsDictionary);

        return $compiledTemplate;
    }

    /**
     * Extract fields to replace from template
     *
     * @param String $template
     * @return Array
     */
    public function extractFieldsFromTemplate(string $template): array
    {
        $fields = [];

        $pregRes = preg_match_all('/(({::)[^>]*?)(.*?)((::})[^>]*?)/', $template, $matches, PREG_SET_ORDER);

        if (is_integer($pregRes)) {
            foreach ($matches as $val) {
                if (is_array($val) && safeCount($val) > 3) {
                    $matchedField = $val[3];
                    $fields[] = $matchedField;
                }
            }
        } else {
            $this->logger->error('Error parsing template: ' . var_export($template, true));
        }

        return $fields;
    }

    /**
     * If field given is a key in a dropdown list then show the label and not the key
     * Also if it's a date type, we have to format it
     *
     * @param String $moduleName
     * @param String $fieldName
     * @param String $fieldValue
     * @return String
     */
    public function getFieldDisplayInSugar(string $moduleName, string $fieldName, string $fieldValue): string
    {
        global $dictionary, $app_list_strings, $timedate;

        $objectName = \BeanFactory::getObjectName($moduleName);

        $fieldDef = $dictionary[$objectName]['fields'][$fieldName];

        if ($fieldDef['type'] == 'enum') {
            if (array_key_exists('options', $fieldDef) && is_string($fieldDef['options'])) {
                $dropdownName = $fieldDef['options'];
                return $app_list_strings[$dropdownName][$fieldValue];
            }
        }

        $dateTypes = ['date', 'datetime', 'datecombo', 'datetimecombo'];
        if (in_array($fieldDef['type'], $dateTypes)) {
            $dateTimeObject = new DateTime($fieldValue);
            if ($dateTimeObject) {
                $fieldValue = $timedate->asUser($dateTimeObject);
            }
        }

        return $fieldValue;
    }

    /**
     * Calculates event end date/datetime based on event start and duration
     *
     * @param Array $event
     * @param SugarBean $calendarBean
     * @return string
     */
    protected function calculateEventEnd(array $event, SugarBean $calendarBean): string
    {
        global $timedate;

        if ($calendarBean->calendar_module == 'Calls' || $calendarBean->calendar_module == 'Meetings') {
            $calendarBean->duration_hours = 'duration_hours';
            $calendarBean->duration_minutes = 'duration_minutes';
        }

        $this->logger->debug('calculateEventEnd ' . var_export($event, true));

        $end = '';

        $startTimestamp = strtotime($event[$calendarBean->event_start]);

        $endTimestamp = false;
        if (!empty($event[$calendarBean->duration_minutes])) {
            $add = '+ ' . $event[$calendarBean->duration_minutes] . ' minutes';
            $endTimestamp = strtotime($add, $startTimestamp);
        }
        if (!empty($event[$calendarBean->duration_hours])) {
            if (is_int($endTimestamp)) {
                $timestampToUse = $endTimestamp;
            } else {
                $timestampToUse = $startTimestamp;
            }

            $add = '+ ' . $event[$calendarBean->duration_hours] . ' hours';
            $endTimestamp = strtotime($add, $timestampToUse);
        }
        if (!empty($event[$calendarBean->duration_days])) {
            if (is_int($endTimestamp)) {
                $timestampToUse = $endTimestamp;
            } else {
                $timestampToUse = $startTimestamp;
            }

            $add = '+ ' . $calendarBean->duration_days . ' days';
            $endTimestamp = strtotime($add, $timestampToUse);
        }

        if (is_int($endTimestamp)) {
            //format event end to the same type as event start
            $startField = $calendarBean->event_start;
            $startFieldType = $this->getFieldType($startField, $calendarBean->calendar_module);

            $endDate = new SugarDateTime();
            $endDate->setTimestamp($endTimestamp);

            $sugarDateTime = $timedate->fromDbType($endDate->format('Y-m-d H:i:s'), 'datetime');

            if ($startFieldType == 'datetime') {
                $endDate = $sugarDateTime->format('Y-m-d H:i:sO');
            }

            $end = $endDate;
        }

        if ($end === '') {
            $end = new SugarDateTime($event[$calendarBean->event_start]);
            $end = $end->format('Y-m-d H:i:sO');
        }
        return $end;
    }

    /**
     * Lists my calendars and others
     * If given, filters are applied
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array Dictionary
     */
    public function listCalendars(ServiceBase $api, array $args): array
    {
        global $current_user;

        $filterDef = [];

        try {
            $filterDef = [
                [
                    'calendar_type' => [
                        '$contains' => 'main',
                    ],
                ],
            ];

            $calendars = [];
            $tempCalendars = $this->getCalendars($filterDef);
            foreach ($tempCalendars as $tempCalendar) {
                $calendars[$tempCalendar['id']] = $tempCalendar;
            }
            $calendarIds = array_keys($calendars);

            if ($args['calendarFilter'] == 'my_calendars') {
                foreach ($calendars as $calId => &$calendar) {
                    $calendar['userId'] = 'current_user';
                    $calendar['userName'] = $current_user->name;
                }
            } elseif ($args['calendarFilter'] == 'other_calendars') {
                $newCalendars = [];

                $users = User::getAllUsers();
                $teams = Team::getArrayAllAvailable(false, $current_user);

                foreach ($args['calendars'] as $key => $calendarConfiguration) {
                    if (!in_array($calendarConfiguration['calendarId'], $calendarIds)) {
                        continue;
                    }
                    if (isset($calendarConfiguration['userId']) && isset($users[$calendarConfiguration['userId']])) {
                        $newCalendar = $calendarConfiguration;
                        $newCalendar['userName'] = $users[$calendarConfiguration['userId']];
                        $newCalendar['name'] = $calendars[$calendarConfiguration['calendarId']]['name'];
                        $newCalendars[] = $newCalendar;
                    }
                    if (isset($calendarConfiguration['teamId']) && isset($teams[$calendarConfiguration['teamId']])) {
                        $newCalendar = $calendarConfiguration;
                        $newCalendar['name'] = $calendars[$calendarConfiguration['calendarId']]['name'];
                        $newCalendar['teamName'] = $teams[$calendarConfiguration['teamId']];
                        $newCalendars[] = $newCalendar;
                    }
                }

                $calendars = $newCalendars;
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return [
            'calendars' => $calendars,
        ];
    }

    /**
     * Get all accessible calendars
     *
     * @param array filterDef
     * @return array
     */
    public function getCalendars(array $filterDef = []): array
    {
        $beansRaw = [];
        $bean = BeanFactory::newBean('Calendar');

        $sq = new SugarQuery();
        $sq->select([
            'id',
            'assigned_user_id',
            'name',
            'calendar_module',
            'event_start',
            'event_end',
            'duration_minutes',
            'duration_hours',
            'duration_days',
            'calendar_type',
            'color',
            'dblclick_event',
            'allow_create',
            'allow_update',
            'allow_delete',
            'calendar_filter',
        ]);

        $sq->from($bean, [
            'team_security' => true,
            'erased_fields' => false,
        ]);

        if (!empty($filterDef)) {
            self::addFilters($filterDef, $sq->where(), $sq);
        }

        $sq->offset(0);

        $result = $sq->execute();

        if (safeCount($result) == 0) {
            return [];
        } else {
            foreach ($result as $row) {
                $objName = BeanFactory::getObjectName($row['calendar_module']);
                $beanRaw = [
                    'calendarId' => $row['id'],
                    'id' => $row['id'],
                    'assigned_user_id' => $row['assigned_user_id'],
                    'name' => $row['name'],
                    'color' => $row['color'],
                    'module' => $row['calendar_module'],
                    'start_field' => $row['event_start'],
                    'end_field' => $row['event_end'],
                    'dblclick_event' => $row['dblclick_event'],
                    'calendar_type' => $row['calendar_type'],
                    'allow_create' => (bool)$row['allow_create'],
                    'allow_update' => (bool)$row['allow_update'],
                    'allow_delete' => (bool)$row['allow_delete'],
                    'objName' => $objName,
                ];

                $beansRaw[] = $beanRaw;
            }
        }

        return $beansRaw;
    }

    /**
     * Get all Users and all Teams
     *
     * When required both, first return Users, then Teams
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getUsersAndTeams(ServiceBase $api, array $args): array
    {
        global $current_user, $sugar_config;
        $db = DBManagerFactory::getInstance();

        $maxPerPage = intVal($sugar_config['list_max_entries_per_page']);
        if (empty($maxPerPage)) {
            $maxPerPage = 20;
        }

        $res = [
            'next_offset' => -1,
            'records' => [],
        ];

        if (!isset($args['offset'])) {
            $args['offset'] = 0;
        }

        $moduleList = [];
        if (empty($args['module_list']) || $args['module_list'] == 'all') {
            $moduleList = ['Users', 'Teams'];
        } else {
            if (is_array($args['module_list'])) {
                $moduleList = $args['module_list'];
            } else {
                $moduleList = [$args['module_list']];
            }
        }

        $sq = new SugarQuery();
        if (in_array('Users', $moduleList)) {
            $userSeed = BeanFactory::newBean('Users');
            $usersQuery = new SugarQuery();
            $usersQuery->select(['id', 'date_entered']);
            $usersQuery->select()->fieldRaw($db->concat('users', ['first_name', 'last_name']), 'name');
            $usersQuery->select()->fieldRaw("'Users'", 'module');
            $usersQuery->from($userSeed);

            if (isset($args['q'])) {
                $queryOr = $usersQuery->where()->queryOr();
                $queryOr->starts('first_name', $args['q']);
                $queryOr->starts('last_name', $args['q']);
            }
            $usersQuery->where()->equals('external_auth_only', 0);
            $usersQuery->where()->equals('portal_only', 0);
            $usersQuery->where()->notEquals('id', $current_user->id);

            $sq->union($usersQuery);
        }

        if (in_array('Teams', $moduleList)) {
            $teamSeed = BeanFactory::newBean('Teams');
            $teamsQuery = new SugarQuery();
            $teamsQuery->select(['id', 'date_entered']);
            $teamsQuery->select()->fieldRaw($db->concat('teams', ['name', 'name_2']), 'name');
            $teamsQuery->select()->fieldRaw("'Teams'", 'module');
            $teamsQuery->from($teamSeed);

            if (isset($args['q'])) {
                $queryOr = $teamsQuery->where()->queryOr();
                $queryOr->starts('name', $args['q']);
                $queryOr->starts('name_2', $args['q']);
            }

            if (!$current_user->isAdmin()) {
                $teamsQuery->join('users', ['joinType' => 'INNER']);
                $teamsQuery->where()->equals('team_memberships.user_id', $current_user->id);
            }

            $sq->union($teamsQuery);
        }

        //make sure to force an order so that the offset is relevant
        $sq->orderBy('date_entered');
        $sq->orderBy('id');

        $sq->offset($args['offset']);
        $sq->limit($maxPerPage + 1);

        $records = $sq->execute();

        if (safeCount($records) == $maxPerPage + 1) {
            array_pop($records);
            $res['next_offset'] = $args['offset'] + $maxPerPage;
        } else {
            $res['next_offset'] = -1;
        }
        foreach ($records as $ey => $row) {
            $row['_module'] = $row['module'];
            unset($row['module']);
            $res['records'][] = $row;
        }

        return $res;
    }

    /**
     * Get all invitees of Calls/Meetings events
     *
     * @param array $records
     * @param string $calendarModule
     * @return array
     */
    public function getAllInvitees(array $records, $calendarModule): array
    {
        $usersInvitees = [];
        $contactsInvitees = [];
        $leadsInvitees = [];
        $recordIds = [];
        $invitees = [];

        foreach ($records as $recordRaw) {
            $recordIds[] = $recordRaw['id'];
        }

        if ($calendarModule == 'Meetings') {
            $recordInviteesOptions = [
                'tableName' => 'meetings_users',
                'eventColumnName' => 'meeting_id',
                'personColumnName' => 'user_id',
            ];
            $usersInvitees = $this->getRecordsInvitees($recordInviteesOptions, $recordIds);

            $recordInviteesOptions = [
                'tableName' => 'meetings_contacts',
                'eventColumnName' => 'meeting_id',
                'personColumnName' => 'contact_id',
            ];
            $contactsInvitees = $this->getRecordsInvitees($recordInviteesOptions, $recordIds);

            $recordInviteesOptions = [
                'tableName' => 'meetings_leads',
                'eventColumnName' => 'meeting_id',
                'personColumnName' => 'lead_id',
            ];
            $leadsInvitees = $this->getRecordsInvitees($recordInviteesOptions, $recordIds);
        } elseif ($calendarModule == 'Calls') {
            $recordInviteesOptions = [
                'tableName' => 'calls_users',
                'eventColumnName' => 'call_id',
                'personColumnName' => 'user_id',
            ];
            $usersInvitees = $this->getRecordsInvitees($recordInviteesOptions, $recordIds);

            $recordInviteesOptions = [
                'tableName' => 'calls_contacts',
                'eventColumnName' => 'call_id',
                'personColumnName' => 'contact_id',
            ];
            $contactsInvitees = $this->getRecordsInvitees($recordInviteesOptions, $recordIds);

            $recordInviteesOptions = [
                'tableName' => 'calls_leads',
                'eventColumnName' => 'call_id',
                'personColumnName' => 'lead_id',
            ];
            $leadsInvitees = $this->getRecordsInvitees($recordInviteesOptions, $recordIds);
        }

        //merge invitees
        $invitees = $this->formatInvitees($usersInvitees, 'Users');
        $contactsInviteesFormatted = $this->formatInvitees($contactsInvitees, 'Contacts');
        foreach ($contactsInviteesFormatted as $recordId => $persons) {
            if (!is_array($invitees[$recordId])) {
                $invitees[$recordId] = [];
            }
            foreach ($persons as $person) {
                $invitees[$recordId][] = $person;
            }
        }

        $leadsInviteesFormatted = $this->formatInvitees($leadsInvitees, 'Leads');
        foreach ($leadsInviteesFormatted as $recordId => $persons) {
            if (!is_array($invitees[$recordId])) {
                $invitees[$recordId] = [];
            }
            foreach ($persons as $person) {
                $invitees[$recordId][] = $person;
            }
        }

        return $invitees;
    }

    /**
     * Format Invitees
     *
     * @param array $recordPersons
     * @param string $module
     * @return array
     */
    public function formatInvitees(array $recordPersons, string $module): array
    {
        $seed = BeanFactory::newBean($module);
        $tableName = $seed->getTableName();

        $inviteesFormatted = [];
        foreach ($recordPersons as $recordId => $invitees) {
            $inviteesFormatted[$recordId] = [];
            $inviteesIds = array_column($invitees, 'person_id');

            $inviteesNames = $this->getInviteesNames($inviteesIds, $tableName);
            foreach ($inviteesNames as $inviteesName) {
                //get the status from invitees by iterating over entire array and get the value
                $acceptStatus = '';
                foreach ($invitees as $invitee) {
                    if ($invitee['person_id'] === $inviteesName['id']) {
                        $acceptStatus = $invitee['acceptStatus'];
                        break;
                    }
                }

                $inviteesFormatted[$recordId][] = [
                    'id' => $inviteesName['id'],
                    'name' => trim($inviteesName['first_name'] . ' ' . $inviteesName['last_name']),
                    'module' => $module,
                    'acceptStatus' => $acceptStatus,
                ];
            }
        }
        return $inviteesFormatted;
    }

    /**
     * Get records invitees
     *
     * Get the invitees and their accept status
     *
     * @param array $options
     * @param array $recordIds
     * @return array
     */
    public function getRecordsInvitees(array $options, array $recordIds): array
    {
        $invitees = [];

        $recordIdsQuoted = [];
        foreach ($recordIds as $recordId) {
            $recordIdsQuoted[] = "'" . $recordId . "'";
        }

        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->select($options['personColumnName'], $options['eventColumnName'], 'accept_status');
        $qb->from($options['tableName']);
        $qb->where($qb->expr()->in($options['eventColumnName'], $recordIdsQuoted));
        $qb->andWhere($qb->expr()->eq('deleted', 0));

        $inviteesData = $qb->execute();
        $inviteesData = $inviteesData->fetchAll();

        foreach ($inviteesData as $inviteesValue) {
            if (!isset($invitees[$inviteesValue[$options['eventColumnName']]]) ||
                !is_array($invitees[$inviteesValue[$options['eventColumnName']]])) {
                $invitees[$inviteesValue[$options['eventColumnName']]] = [];
            }
            $invitees[$inviteesValue[$options['eventColumnName']]][] = [
                'person_id' => $inviteesValue[$options['personColumnName']],
                'acceptStatus' => $inviteesValue['accept_status'],
            ];
        }

        return $invitees;
    }

    /**
     * Get invitees names
     *
     * @param array $userIds
     * @param string $moduleTable
     * @return array User Id and User Name
     */
    public function getInviteesNames(array $userIds, string $moduleTable): array
    {
        $userIdsQuoted = [];
        foreach ($userIds as $userId) {
            $userIdsQuoted[] = "'" . $userId . "'";
        }

        $qb = DBManagerFactory::getConnection()->createQueryBuilder();

        $qb->select('id', 'first_name', 'last_name');
        $qb->from($moduleTable);
        $qb->where($qb->expr()->in('id', $userIdsQuoted));
        $qb->andWhere($qb->expr()->eq('deleted', 0));

        $result = $qb->execute();
        $result = $result->fetchAll();

        return $result;
    }

    /**
     * Get calendar definitions based on ids given
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getCalendarDefs(ServiceBase $api, array $args): array
    {
        $calendarIds = [];
        foreach ($args['calendars'] as $key => $calendar) {
            $calendarIds[] = $calendar['calendarId'];
        }

        $filterDef = [[
            'id' => [
                '$in' => $calendarIds,
            ],
        ]];

        $calendarsRes = $this->getCalendars($filterDef);
        return $calendarsRes;
    }

    /**
     * Get a list of modules which have Calendar Definitions created
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function getCalendarModules(ServiceBase $api, array $args): array
    {
        $result = [
            'modules' => [],
        ];

        if (!$this->hasAccess('Calendar', 'view')) {
            throw new SugarApiExceptionNotAuthorized('No access to view modules used in Calendar Definitions');
        }

        $calendarModulesInDb = $this->queryCalendarModules();
        if (is_array($calendarModulesInDb) == true && safeCount($calendarModulesInDb) > 0) {
            foreach ($calendarModulesInDb as $row) {
                if ($this->hasAccess($row['calendar_module'], 'save') && $row['allow_create']) {
                    $objName = BeanFactory::getObjectName($row['calendar_module']);
                    $result['modules'][$row['calendar_module']] = [
                        'objName' => $objName,
                    ];
                }
            }

            return $result;
        }

        return $result;
    }

    /**
     * Get access to module for user logged
     *
     * @param string $module
     * @param string $accessNeeded
     * @return bool
     */
    public function hasAccess($module, $accessNeeded): bool
    {
        $seed = BeanFactory::newBean($module);

        return $seed->ACLAccess($accessNeeded);
    }

    /**
     * Get list of modules from database
     *
     * @return array
     */
    public function queryCalendarModules(): array
    {
        $seed = BeanFactory::newBean('Calendar');

        $sq = new SugarQuery();
        $sq->select(['calendar_module', 'allow_create']);
        $sq->from($seed, ['erased_fields' => false]);
        $sq->distinct(true);

        $result = $sq->execute();
        return $result;
    }

    /**
     * Map from global search api arguments to search params expected by
     * legacy invitee search code
     *
     * @param array $args
     * @return array
     */
    protected function buildSearchParams(array $args)
    {
        $modules = explode(',', $args['module_list']);
        $searchFields = explode(',', $args['search_fields']);
        $fieldList = explode(',', $args['fields']);
        $fieldList = array_merge($fieldList, $searchFields);

        $conditions = [];
        foreach ($searchFields as $searchField) {
            $conditions[] = [
                'name' => $searchField,
                'op' => 'starts_with',
                'value' => $args['q'],
            ];
        }

        return [
            [
                'modules' => $modules,
                'group' => 'or',
                'field_list' => $fieldList,
                'conditions' => $conditions,
            ],
        ];
    }

    /**
     * Run the the legacy invitee query
     *
     * @param $params
     * @return array
     */
    protected function runInviteeQuery($params)
    {
        $requestId = '1'; //not really used
        $jsonServer = new LegacyJsonServer();
        return $jsonServer->query($requestId, $params, true);
    }

    /**
     * Map from legacy invitee search code's result format to a format
     * that is closer to what global search returns
     *
     * Pagination is not supported
     *
     * @param ServiceBase $api
     * @param array $args
     * @param $searchResults
     * @return array
     */
    protected function transformInvitees(ServiceBase $api, array $args, $searchResults)
    {
        $resultList = $searchResults['result']['list'];
        $records = [];
        foreach ($resultList as $result) {
            if (!empty($args['erased_fields'])) {
                $options = ['erased_fields' => true, 'use_cache' => false, 'encode' => false];
                $result['bean'] = BeanFactory::retrieveBean($result['bean']->module_dir, $result['bean']->id, $options);
            }
            if (isset($result['bean']) && $result['bean'] instanceof SugarBean) {
                $record = $this->formatBean($api, $args, $result['bean']);
                $highlighted = $this->getMatchedFields($args, $record, 1);
                $record['_search'] = [
                    'highlighted' => $highlighted,
                ];
                $records[] = $record;
            }
        }

        return [
            'next_offset' => -1,
            'records' => $records,
        ];
    }

    /**
     * Returns an array of fields that matched search query
     *
     * @param array $args Api arguments
     * @param array $record Search result formatted from bean into array form
     * @param int $maxFields Number of highlighted fields to return, 0 = all
     *
     * @return array matched fields key value pairs
     */
    protected function getMatchedFields(array $args, $record, $maxFields = 0)
    {
        $query = $args['q'];
        $searchFields = explode(',', $args['search_fields']);

        $matchedFields = [];
        foreach ($searchFields as $searchField) {
            if (!isset($record[$searchField])) {
                continue;
            }

            $fieldValues = [];
            if ($searchField == 'email') {
                //can be multiple email addresses
                foreach ($record[$searchField] as $email) {
                    $fieldValues[] = $email['email_address'];
                }
            } elseif (is_string($record[$searchField])) {
                $fieldValues = [$record[$searchField]];
            }

            foreach ($fieldValues as $fieldValue) {
                if (stripos($fieldValue, (string) $query) !== false) {
                    $matchedFields[$searchField] = [$fieldValue];
                }
            }
        }

        $ret = [];
        if (!empty($matchedFields) && is_array($matchedFields)) {
            $highlighter = new SugarSearchEngineHighlighter();
            $highlighter->setModule($record['_module']);
            $ret = $highlighter->processHighlightText($matchedFields);
            if ($maxFields > 0) {
                $ret = array_slice($ret, 0, $maxFields);
            }
        }

        return $ret;
    }

    /**
     * Get iCal Configurations UID
     *
     * Add calendar configurations in the database and return the uid
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getICalConfigurationsUID(ServiceBase $api, array $args): array
    {
        global $timedate, $current_user;

        $data = $args['calendarConfigurations'];

        $data = json_encode($data);

        $id = Uuid::uuid1();
        $dateEntered = $timedate->nowDb();

        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->insert('calendar_ical_configs')
            ->values([
                'id' => $qb->createPositionalParameter($id),
                'date_entered' => $qb->createPositionalParameter($dateEntered),
                'calendar_configurations' => $qb->createPositionalParameter($data),
            ]);

        $qb->execute();

        $publishKey = $current_user->getPreference('calendar_publish_key');
        $publishKey = empty($publishKey) ? '' : $publishKey;

        return [
            'key' => $publishKey,
            'calendarConfigurationUID' => $id,
        ];
    }

    /**
     * Returns a url where a specific calendar data can be found
     *
     * @param ServiceBase $api
     * @param array $args
     * @return string
     */
    public function getICalPublishUrl(ServiceBase $api, array $args): string
    {
        global $sugar_config, $current_user, $timedate;

        $restService = new RestService();
        $apiVersion = $restService->api_settings['maxVersion'];
        $apiVersion = str_replace('.', '_', $apiVersion);

        $data = $args['calendarConfigurations'];

        $data = json_encode($data);

        $id = Uuid::uuid1();
        $dateEntered = $timedate->nowDb();

        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->insert('calendar_ical_configs')
            ->values([
                'id' => $qb->createPositionalParameter($id),
                'date_entered' => $qb->createPositionalParameter($dateEntered),
                'calendar_configurations' => $qb->createPositionalParameter($data),
            ]);

        $qb->execute();

        $publishKey = $current_user->getPreference('calendar_publish_key');
        $publishKey = empty($publishKey) ? '' : $publishKey;
        if ($publishKey === '') {
            return 'empty_publish_key';
        }

        $url = rtrim($sugar_config['site_url'], '/') . "/rest/v{$apiVersion}/Calendar/getICalData"
            . "?type=ics&user_id={$current_user->id}&key={$publishKey}&calendarsUID={$id}";
        return $url;
    }

    /**
     * Get iCal data
     *
     * This Api will just echo its content
     *
     * @param ServiceBase $api
     * @param array $args
     * @return void
     */
    public function getICalData(ServiceBase $api, array $args): void
    {
        require_once 'include/entryPoint.php';
        require_once 'modules/Calendar_iCals/HTTP_WebDAV_Server_Calendar_iCal.php';

        $server = new HTTP_WebDAV_Server_Calendar_iCal();
        $server->ServeICalRequest();
        sugar_cleanup();
        die();
    }

    /**
     * Get all related records
     *
     * @param array $relOpts
     * @param array $data
     * @return array
     */
    public function getAllRelatedRecords(array $relOpts, array $data): array
    {
        global $db;

        $dictionaryIds = [];
        $recordIdsQuoted = [];

        if ($relOpts['side'] == REL_RHS) {
            $targetModuleKey = $relOpts['lhsKey'];
            $sourceModuleKey = $relOpts['rhsKey'];
        } else {
            $targetModuleKey = $relOpts['rhsKey'];
            $sourceModuleKey = $relOpts['lhsKey'];
        }

        foreach ($data as $recordId) {
            $recordIdsQuoted[] = $db->quoted($recordId['id']);
        }

        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->select([$targetModuleKey, $sourceModuleKey]);
        $qb->from($relOpts['tableName']);
        $qb->where($qb->expr()->in($sourceModuleKey, $recordIdsQuoted));
        $qb->andWhere($qb->expr()->eq('deleted', 0));

        $relIds = $qb->execute();
        $relIds = $relIds->fetchAll();

        foreach ($relIds as $relValue) {
            $dictionaryIds[$relValue[$sourceModuleKey]] = $relValue[$targetModuleKey];
        }

        return $dictionaryIds;
    }
}
