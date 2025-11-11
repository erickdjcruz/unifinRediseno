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

use Sugarcrm\Sugarcrm\Util\Uuid;

/**
 * Class for constructing the iCal response string for the current user.
 *
 * @see vCal
 */
class Calendar_iCal extends iCal
{
    public const DATE_FORMAT = 'Ymd';

    /**
     * {@inheritDoc}
     */
    protected function createSugarIcal(&$user_bean, &$start_date_time, &$end_date_time, $dtstamp)
    {
        global $current_user, $sugar_config;

        $timedate = Timedate::getInstance();

        $restService = new RestService();

        $start_date_time = date('Y') . '-01-01 00:00:00';
        $end_date_time = date('Y') . '-12-31 11:59:59';

        $ical_array = [];
        $calendars = $_REQUEST['calendarConfigurations'];

        $calendarSettings = [
            'startDate' => $start_date_time,
            'endDate' => $end_date_time,
            'calendarConfigurations' => $calendars,
        ];

        $calendarApi = new CalendarApi();
        $calendarApi->export = true;
        $restService = new RestService();
        $eventsResult = $calendarApi->getEvents($restService, $calendarSettings);
        $events = $eventsResult['data'];

        foreach ($events as $idx => $event) {
            $db = DBManagerFactory::getInstance();
            $calendarSeed = BeanFactory::getBean('Calendar', $event['calendarId']);

            $startIsDateType = false;
            $startField = $calendarSeed->event_start;

            $eventSeed = BeanFactory::newBean($calendarSeed->calendar_module);

            $startFieldType = $db->getFieldType($eventSeed->field_defs[$startField]);
            if ($startFieldType == 'date') {
                $startIsDateType = true;
            }

            $endIsDateType = false;
            $endField = $calendarSeed->event_end;

            if (!empty($endField)) {
                $endFieldType = $db->getFieldType($eventSeed->field_defs[$endField]);
                if ($endFieldType == 'date') {
                    $endIsDateType = true;
                }
            }

            if ($startIsDateType) {
                //if it's a date type it does not have time.
                //we use fromString because we don't expect it be converted from FMT
                $start = $timedate->fromString($event['start']);
            } elseif (isset($event['isAllDay']) && $event['isAllDay'] == true) {
                $start = $timedate->fromString($event['start']);
            } else {
                //this will convert from GMT to current user timezone
                $start = $timedate->fromDbFormat($event['start'], 'Y-m-d H:i:sO');
            }
            if ($endIsDateType) {
                $end = $timedate->fromString($event['end']);
            } elseif (isset($event['isAllDay']) && $event['isAllDay'] === true) {
                $end = $timedate->fromString($event['end']);
            } else {
                $end = $timedate->fromDbFormat($event['end'], 'Y-m-d H:i:sO');
            }

            $ical_array[] = ['BEGIN', 'VEVENT'];
            $ical_array[] = ['SUMMARY', html_entity_decode($event['title'], ENT_QUOTES)];

            if ((isset($event['isAllDay']) && $event['isAllDay'] == true) || $startIsDateType ||
                $event['start'] === $event['end']) {
                $startFormatted = $timedate->tzUser($start, $current_user)->format(self::DATE_FORMAT);
                $ical_array[] = [
                    'DTSTART;VALUE=DATE', str_replace('Z', '', $startFormatted),
                ];
            } else {
                $ical_array[] = [
                    'DTSTART;TZID=' . $current_user->getPreference('timezone'),
                    str_replace('Z', '', $timedate->tzUser($start, $current_user)->format(self::UTC_FORMAT)),
                ];
            }

            if ((isset($event['isAllDay']) && $event['isAllDay'] == true) || $endIsDateType ||
                $event['start'] === $event['end']) {
                //this one day addition is a fix for google calendar / outlook which expect end date to be the next day
                $oneDay = new DateInterval('P1D');
                $end = $end->add($oneDay);

                $endFormatted = $timedate->tzUser($end, $current_user)->format(self::DATE_FORMAT);
                $ical_array[] = [
                    'DTEND;VALUE=DATE', str_replace('Z', '', $endFormatted),
                ];
            } else {
                //sugar's format
                $ical_array[] = [
                    'DTEND;TZID=' . $current_user->getPreference('timezone'),
                    str_replace('Z', '', $timedate->tzUser($end, $current_user)->format(self::UTC_FORMAT)),
                ];
            }

            $now_date_time = $timedate->getNow(true);
            $utc_now_time = $this->getUtcDateTime($now_date_time);
            $ical_array[] = ['DTSTAMP', $utc_now_time];
            $ical_array[] = ['DESCRIPTION', html_entity_decode($event['ical_event_template'], ENT_QUOTES)];

            $sugarSiteId = rtrim($sugar_config['site_url'], '/');
            $ical_array[] = [
                'URL;VALUE=URI',
                "{$sugarSiteId}/#{$event['module']}/{$event['id']}",
            ];

            $uid = Uuid::uuid1();
            $ical_array[] = ['UID', $uid];
            $ical_array[] = ['END', 'VEVENT'];
        }

        $str = vCal::create_ical_string_from_array($ical_array);
        return $str;
    }
}
