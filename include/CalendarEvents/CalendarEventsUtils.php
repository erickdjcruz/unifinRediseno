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

use RRule\RRule;
use RRule\RSet;
use RRule\RfcParser;

class CalendarEventsUtils
{
    private $old_assigned_user_id = null;
    private static $instance = null;

    private $RRULE_LANGUAGE_MAPPING_TO_SUGAR = [
        'de_de' => 'de',  // German
        'en_us' => 'en',  // English (US)
        'es_es' => 'es',  // Spanish
        'fa_ir' => 'fa',  // Persian (Farsi)
        'fi_fi' => 'fi',  // Finnish
        'fr_fr' => 'fr',  // French
        'it_it' => 'it',  // Italian
        'nl_nl' => 'nl',  // Dutch
        'pl_pl' => 'pl',  // Polish
        'pt_pt' => 'pt',  // Portuguese
        'sv_se' => 'sv',  // Swedish
    ];

    private $DEFAULT_LANGUAGE = 'en';

    /**
     * inherit doc
     */
    private function __construct()
    {
    }

    /**
     * Get a singleton instance of CalendarEventsUtils
     *
     * @return CalendarEventsUtils
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new CalendarEventsUtils();
        }

        return self::$instance;
    }

    /**
     * Return old assigned user
     *
     * @param string module
     * @param string|null user id
     *
     * @return string old user id
     */
    public function getOldAssignedUser($module, $id = null)
    {
        if ($this->old_assigned_user_id === null) { // lazy load
            $this->setOldAssignedUser($module, $id);
        }

        return $this->old_assigned_user_id;
    }

    /**
     * Set old assigned user
     *
     * @param string value
     */
    public function setOldAssignedUserValue($value)
    {
        $this->old_assigned_user_id = $value;
    }

    /**
     * Set the old assigned user
     *
     * @param string module
     * @param string|null user id
     */
    public function setOldAssignedUser($module, $id = null)
    {
        $this->old_assigned_user_id = '';

        if (!empty($module) && !empty($id)) {
            $old_record = BeanFactory::getBean($module, $id);

            if (!empty($old_record->assigned_user_id)) {
                $this->old_assigned_user_id = $old_record->assigned_user_id;
            }
        }
    }

    /**
     * Convert A Date, Time  or DateTime String from one format to Another
     *
     * @param string type of the second argument : one of 'date', 'time', 'datetime', 'datetimecombo'
     * @param string formatted date, time or datetime field in DB, ISO, or User Format
     * @param string output format - one of: 'db', 'iso' or 'user'
     * @param User whose formatting preferences are to be used if output format is 'user'
     *
     * @return string formatted result
     */
    public function formatDateTime($type, $dtm, $toFormat, $user = null)
    {
        $result = '';

        if (empty($user)) {
            global $current_user;

            $user = $current_user;
        }

        $sugarDateTime = $this->getSugarDateTime($type, $dtm, $user);

        if (!empty($sugarDateTime)) {
            $result = $sugarDateTime->formatDateTime($type, $toFormat, $user);
        }

        return $result;
    }

    /**
     * Return a SugarDateTime Object given any Date to Time Format
     * @param string type of the second argument : one of 'date', 'time', 'datetime', 'datetimecombo'
     * @param string  formatted date, time or datetime field in DB, ISO, or User Format
     * @param User whose timezone preferences are to be used (optional - defaults to current user)
     * @return SugarDateTime
     */
    public function getSugarDateTime($type, $dtm, $user = null)
    {
        global $timedate;
        $sugarDateTime = null;

        if (!empty($dtm)) {
            $sugarDateTime = $timedate->fromUserType($dtm, $type, $user);

            if (empty($sugarDateTime)) {
                $sugarDateTime = $timedate->fromDBType($dtm, $type);
            }

            if (empty($sugarDateTime)) {
                switch ($type) {
                    case 'time':
                        $sugarDateTime = $timedate->fromIsoTime($dtm);
                        break;
                    case 'date':
                    case 'datetime':
                    case 'datetimecombo':
                    default:
                        $sugarDateTime = $timedate->fromIso($dtm);
                        break;
                }
            }
        }

        return $sugarDateTime;
    }

    /**
     * Return Configured recurrence limit.
     * @return int
     */
    public function getRecurringLimit()
    {
        return SugarConfig::getInstance()->get('calendar.max_repeat_count', 1000);
    }

    /**
     * Rebuild the FreeBusy Vcal Cache for specified user
     */
    public function rebuildFreeBusyCache(User $user)
    {
        vCal::cache_sugar_vcal($user);
    }

    /**
     * Find first day of week according to user's settings
     * @param SugarDateTime $date
     * @return SugarDateTime $date
     */
    public function get_first_day_of_week(SugarDateTime $date)
    {
        global $current_user;

        $fdow = $current_user->get_first_day_of_week();

        if ($date->day_of_week < $fdow) {
            $date = $date->get('-7 days');
        }

        return $date->get_day_by_index_this_week($fdow);
    }

    /**
     * Build the set of Dates/Times for the Recurring Meeting parameters specified
     *
     * @param string $date_start
     * @param array $params
     *
     * @return array datetime Strings
     */
    public function buildRecurringSequence($dateStart, $params)
    {
        global $timedate;

        $options = $params;
        $type = $params['type'];

        if ($type === 'Weekly') {
            $dow = $params['dow'];

            if ($dow === '') {
                return [];
            }
        }

        $options['type'] = $type;
        $interval = intval($params['interval']);

        if ($interval < 1) {
            $interval = 1;
        }

        $options['interval'] = $interval;

        if (!empty($params['count'])) {
            $count = $params['count'];

            if ($count < 1) {
                $count = 1;
            }
        } else {
            $count = 0;
        }

        $options['count'] = $count;
        $options['until'] = empty($params['until']) ? '' : $params['until'];

        if ($options['count'] === 0 && empty($options['until'])) {
            return [];
        }

        $start = SugarDateTime::createFromFormat($timedate->get_date_time_format(), $dateStart);
        $options['start'] = $start;

        if (!empty($options['until'])) {
            $end = SugarDateTime::createFromFormat($timedate->get_date_format(), $options['until']);
            $end->setTime(23, 59, 59);   // inclusive
        } else {
            $end = $start;
        }

        $options['end'] = $end;
        $current = clone $start;
        $scratchPad = [];
        $days = [];

        if ($params['type'] === 'Monthly' && !empty($params['selector']) && $params['selector'] === 'Each') {
            if (!empty($params['days'])) {
                $dArray = explode(',', $params['days']);

                foreach ($dArray as $day) {
                    $day = intval($day);

                    if ($day >= 1 && $day <= 31) {
                        $days[$day] = true;
                    }
                }

                ksort($days);
                $days = array_keys($days);
            }
        }

        $options['days'] = $days;
        $scratchPad['days'] = $days;

        $scratchPad['ResultTotal'] = 0;
        $scratchPad['Results'] = [];

        $limit = SugarConfig::getInstance()->get('calendar.max_repeat_count', 1000);
        $loop = true;

        while ($loop) {
            switch ($type) {
                case 'Daily':
                    $loop = $this->nextDaily($current, $interval, $options, $scratchPad);
                    break;
                case 'Weekly':
                    $loop = $this->nextWeekly($current, $interval, $options, $scratchPad);
                    break;
                case 'Monthly':
                    $loop = $this->nextMonthly($current, $interval, $options, $scratchPad);
                    break;
                case 'Yearly':
                    $loop = $this->nextYearly($current, $interval, $options, $scratchPad);
                    break;
                default:
                    return [];
            }

            if ($scratchPad['ResultTotal'] > $limit + 100) {
                break;
            }
        }

        return $scratchPad['Results'];
    }

    /**
     * Determine whether recurrence iteration meets the  count or until terminating criteria
     * and Update the Result Array and Result Count Totals Appropriately if the current Date
     * is part of the recurring result set
     *
     * @param SugarDateTime $current
     * @param array $options : the recurrence rules in effect
     * @param array $scratchPad : Scratchpad Area for intermediate and final result computation
     *
     * @return bool  true=Complete   false=Incomplete
     */
    public function isComplete($current, $options, &$scratchPad)
    {
        global $timedate;

        if (($options['count'] === 0 &&
                !empty($options['until']) &&
                !empty($options['end']) &&
                $current->format('U') <= $options['end']->format('U')) ||
            ($options['count'] > 0 &&
                $scratchPad['ResultTotal'] < $options['count'])
        ) {
            $scratchPad['Results'][] = $current->format($timedate->get_date_time_format());
            $scratchPad['ResultTotal']++;

            return false;
        }

        return true;
    }

    /**
     * Process the current Datetime for Repeat type = 'Daily'
     * @param SugarDateTime $current : the next Date to be considered as a Result Candidate
     * @param array $interval : interval size
     * @param array $options : array of processing options
     * @param array $scratchPad : Scratchpad Area for intermediate and final result computation
     * @return boolean : true=continue false=quit
     */
    public function nextDaily($current, $interval, $options, &$scratchPad)
    {
        if (!$this->isComplete($current, $options, $scratchPad)) {
            $current->modify("+{$interval} Days");

            return true; // Continue
        }

        return false;
    }

    /**
     * Process the current Datetime for Repeat type = 'Weekly'
     *
     * @param SugarDateTime $current : the next Date to be considered as a Result Candidate
     * @param int $interval : interval size
     * @param array $options : array of processing options
     * @param array $scratchPad : Scratchpad Area for intermediate and final result computation
     *
     * @return boolean : true=continue false=quit
     */
    public function nextWeekly($current, $interval, $options, &$scratchPad)
    {
        $dow = $current->getDayOfWeek();
        $days = 0;
        $daysInWeek = 7;

        while (($pos = strpos($options['dow'], "{$dow}")) === false) {
            $dow++;
            $dow = $dow % $daysInWeek;
            $days++;
        }

        $current->modify("+{$days} Days");

        if (!$this->isComplete($current, $options, $scratchPad)) {
            if ($pos + 1 === strlen($options['dow'])) {
                $skip = ($daysInWeek * ($interval - 1)) + 1;
                $current->modify("+{$skip} Days");
            } else {
                $current->modify('+1 Days');
            }

            return true; // Continue
        }

        return false;
    }

    /**
     * Process the current Datetime for Repeat type = 'Monthly'
     *
     * @param SugarDateTime $current : the next Date to be considered as a Result Candidate
     * @param array $interval : interval size
     * @param array $options : array of processing options
     * @param array $scratchPad : Scratchpad Area for intermediate and final result computation
     *
     * @return boolean : true=continue false=quit
     */
    public function nextMonthly($current, $interval, $options, &$scratchPad)
    {
        global $app_list_strings;

        if (empty($options['selector']) || $options['selector'] === 'None') {
            if (!$this->isComplete($current, $options, $scratchPad)) {
                $current->modify("+{$interval} Months");

                return true; // Continue
            }

            return false; // Quit
        }

        switch ($options['selector']) {
            case 'On':
                if (!empty($options['ordinal']) && !empty($options['unit'])) {
                    $ordinal = $options['ordinal'];
                    $unit = $options['unit'];

                    $current->setDateForFirstDayOfMonth();

                    if (!empty($app_list_strings['repeat_ordinal_dom'][$ordinal]) &&
                        !empty($app_list_strings['repeat_unit_dom'][$unit])
                    ) {
                        $offset = $this->getOffsetFromOrdinal($ordinal);
                        $targetDay = $this->getTargetDayFromUnit($unit);

                        $result = null;
                        $last = ($offset == -1);

                        if ($targetDay >= 0) {    // Day Of Week (0=>6)
                            $dates = $current->getMonthDatesForDaysOfWeek([$targetDay]);

                            if ($last) {
                                $offset = safeCount($dates) - 1;
                            }

                            if (isset($dates[$offset])) {
                                $result = $dates[$offset];
                            }
                        } elseif ($unit === 'Day') {
                            if ($last) {
                                $day = $current->getDaysInMonth();
                            } else {
                                $day = $offset + 1;
                            }

                            $result = $current->setDate($current->getYear(), $current->getMonth(), $day);
                        } else {
                            if ($unit === 'WD') { // WeekDay
                                $dates = $current->getMonthDatesForNonWeekEndDays();
                            } else { // 'WE' = Weekend Day
                                $dates = $current->getMonthDatesForWeekEndDays();
                            }

                            if ($last) {
                                $offset = safeCount($dates) - 1;
                            }

                            if (isset($dates[$offset])) {
                                $result = $dates[$offset];
                            }
                        }

                        if (empty($result)) { // Month does not have an instance of the requested Date (e.g. fifth Fri)
                            $current->setDateForFirstDayOfMonth();
                            $current->modify("+{$interval} Months");

                            return true;  // Bypass and Continue
                        }

                        $startDatetime = $options['start'];
                        $temp = clone $startDatetime;

                        $temp->setDate($result->getYear(), $result->getMonth(), $result->getDay());

                        $diffInterval = $startDatetime->diff($temp);

                        if ($diffInterval->invert) {
                            $current->setDateForFirstDayOfMonth();
                            $current->modify("+{$interval} Months");

                            return true;  // Bypass and Continue
                        }

                        if (!$this->isComplete($result, $options, $scratchPad)) {
                            $current->setDateForFirstDayOfMonth();
                            $current->modify("+{$interval} Months");

                            return true; // Continue
                        }

                        return false;  // Quit
                    }
                }

                return false;  // Quit
                break;
            case 'Each':
                /* Current Day of Month need not be considered in the "Each" case - We have specific days to consider */
                $current->setDateForFirstDayOfMonth();

                $startDatetime = $options['start'];
                $temp = clone $startDatetime;

                foreach ($options['days'] as $day) {
                    if ($day <= $current->days_in_month) {
                        $temp->setDate($current->getYear(), $current->getMonth(), $day);

                        $diffInterval = $startDatetime->diff($temp);

                        // Now or in the future
                        if ($diffInterval->invert === 0 && $this->isComplete($temp, $options, $scratchPad)) {
                            return false;  // Quit
                        }
                    }
                }
                $current->modify("+{$interval} Months");

                return true; // Continue
                break;
        }
        return false;  // Quit
    }

    /**
     * Process the current Datetime for Repeat type = 'Yearly'
     *
     * @param SugarDateTime $current : the next Date to be considered as a Result Candidate
     * @param array $interval : interval size
     * @param array $options : array of processing options
     * @param array $scratchPad : Scratchpad Area for intermediate and final result computation
     *
     * @return boolean : true=continue false=quit
     */
    public function nextYearly($current, $interval, $options, &$scratchPad)
    {
        global $app_list_strings;

        if (empty($options['selector']) || $options['selector'] === 'None') {
            if (!$this->isComplete($current, $options, $scratchPad)) {
                $current->modify("+{$interval} Years");

                return true; // Continue
            }

            return false; // Quit
        }

        $startDatetime = $options['start'];
        $temp = clone $startDatetime;

        $temp->setDate($current->getYear(), $current->getMonth(), $current->getDay());
        $diffInterval = $startDatetime->diff($temp);

        if ($diffInterval->invert) {
            $current->modify("+{$interval} Years");

            return true;  // PastDate: Bypass and Continue
        }

        if ($options['selector'] === 'On') {
            if (!empty($options['ordinal']) && !empty($options['unit'])) {
                $ordinal = $options['ordinal'];
                $unit = $options['unit'];

                if (!empty($app_list_strings['repeat_ordinal_dom'][$ordinal]) &&
                    !empty($app_list_strings['repeat_unit_dom'][$unit])
                ) {
                    $offset = $this->getOffsetFromOrdinal($ordinal);
                    $targetDay = $this->getTargetDayFromUnit($unit);

                    $result = null;
                    $last = ($offset === -1);

                    if ($targetDay >= 0) {    // Day Of Week (0=>6)
                        $dates = $current->getYearDatesForDaysOfWeek([$targetDay]);

                        if ($last) {
                            $offset = safeCount($dates) - 1;
                        }

                        if (isset($dates[$offset])) {
                            $result = $dates[$offset];
                        }
                    } elseif ($unit === 'Day') {
                        if ($last) {
                            $current->setDate($current->getYear(), 12, 31);
                        } else {
                            $day = $offset + 1;
                            $current->setDate($current->getYear(), 1, $day);
                        }

                        $result = $current;
                    } else {
                        if ($last) {
                            $current->setDate($current->getYear(), 12, 1);

                            if ($unit === 'WD') { // WeekDay
                                $dates = $current->getMonthDatesForNonWeekEndDays();
                            } else { // 'WE' = Weekend Day
                                $dates = $current->getMonthDatesForWeekEndDays();
                            }

                            $offset = safeCount($dates) - 1;

                            if (isset($dates[$offset])) {
                                $result = $dates[$offset];
                            }
                        } else {
                            $current->setDate($current->getYear(), 1, 1);

                            if ($unit === 'WD') { // WeekDay
                                $dates = $current->getMonthDatesForNonWeekEndDays();
                            } else { // 'WE' = Weekend Day
                                $dates = $current->getMonthDatesForWeekEndDays();
                            }

                            if (isset($dates[$offset])) {
                                $result = $dates[$offset];
                            }
                        }
                    }

                    if (empty($result)) { // Month does not have an instance of the requested Date (e.g. fifth Fri)
                        $current->modify("+{$interval} Years");

                        return true;  // Bypass and Continue
                    }

                    $startDatetime = $options['start'];
                    $temp = clone $startDatetime;

                    $temp->setDate($result->getYear(), $result->getMonth(), $result->getDay());

                    $diffInterval = $startDatetime->diff($temp);

                    if ($diffInterval->invert) {
                        $current->modify("+{$interval} Years");

                        return true;  // Bypass and Continue
                    }

                    if (!$this->isComplete($result, $options, $scratchPad)) {
                        $current->modify("+{$interval} Years");

                        return true; // Continue
                    }

                    return false;  // Quit - Complete
                }
            }

            return false;  // Quit  -  Ordinal and/or Unit Options invalid or missing
        }
        return false;  // Quit   -  Selector option invalid
    }

    public function getOffsetFromOrdinal($ordinal)
    {
        switch ($ordinal) {
            case 'first':
                $offset = 0;
                break;
            case 'second':
                $offset = 1;
                break;
            case 'third':
                $offset = 2;
                break;
            case 'fourth':
                $offset = 3;
                break;
            case 'fifth':
                $offset = 4;
                break;
            default: // 'last'
                $offset = -1;
                break;
        }

        return $offset;
    }

    public function getTargetDayFromUnit($unit)
    {
        switch ($unit) {
            case 'Sun':
                $targetDay = SugarDateTime::DOW_SUN;
                break;
            case 'Mon':
                $targetDay = SugarDateTime::DOW_MON;
                break;
            case 'Tue':
                $targetDay = SugarDateTime::DOW_TUE;
                break;
            case 'Wed':
                $targetDay = SugarDateTime::DOW_WED;
                break;
            case 'Thu':
                $targetDay = SugarDateTime::DOW_THU;
                break;
            case 'Fri':
                $targetDay = SugarDateTime::DOW_FRI;
                break;
            case 'Sat':
                $targetDay = SugarDateTime::DOW_SAT;
                break;
            default: // Not Day of the Week: WD (Weekday) or WE (Weekend Day)
                $targetDay = -1;
                break;
        }

        return $targetDay;
    }

    /**
     * Get list of needed fields for modules
     * @return array
     */
    public function get_fields()
    {
        return [
            'Meetings' => [
                'name',
                'duration_hours',
                'duration_minutes',
                'status',
                'related_to',
            ],
            'Calls' => [
                'name',
                'duration_hours',
                'duration_minutes',
                'status',
                'related_to',
            ],
            'Tasks' => [
                'name',
                'status',
                'related_to',
            ],
        ];
    }

    /**
     * Gets all the occurrences from the rset
     * @param object $rset
     * @return array
     */
    public function getOccurrences(object $rset): array
    {
        global $timedate;
        $occurrences = [];
        foreach ($rset as $occurrence) {
            $occurrences[] = $occurrence->format($timedate->get_date_time_format());
        }

        return $occurrences;
    }

    /**
     * Get Rrule from translation
     * @param array $translation rrule
     * @return string
     */
    public function getRruleString(array $translation): string
    {
        global $current_user;

        $timezone = $current_user->getPreference('timezone');
        $timezone = $timezone ? $timezone : 'UTC';

        $rruleStringArray = [];

        foreach ($translation['RRULE'] as $key => $value) {
            $rruleStringArray[] = $key . '=' . $value;
        }

        $rrule = 'RRULE:' . implode(';', $rruleStringArray);
        $dtstart = 'DTSTART;TZID=' . $timezone . ':' . $translation['DTSTART'];
        $rset = $dtstart . PHP_EOL . $rrule;

        return $rset;
    }

    /**
     * Create an Rrule string from an array
     * @param array $rrule
     * @return string
     */
    public function rruleStringFromArray(array $rrule): string
    {
        $rruleDescription = '';

        foreach ($rrule as $rsetKey => $rsetValue) {
            $rruleDescription .= $rsetKey . '=' . $rsetValue . ';';
        }


        return $rruleDescription;
    }

    /**
     * Create an Rrule array from a string
     * @param string $rruleString
     * @return array
     */
    public function rruleArrayFromString(string $rruleString): array
    {
        $rruleParts = explode(';', $rruleString);
        $rrule = [];

        foreach ($rruleParts as $part) {
            $keyValue = explode('=', $part, 2);

            if (count($keyValue) === 2) {
                $rrule[$keyValue[0]] = $keyValue[1];
            }
        }


        return $rrule;
    }

    /**
     * Creates the rset and returns the occurrences
     * @param string $givenRrule
     * @return array occurrences
     */
    public function createRsetAndGetOccurrences(string $givenRrule = ''): array
    {
        global $timedate;

        $rruleProps = RfcParser::parseRRule($givenRrule);
        $rruleProps = array_change_key_case($rruleProps, CASE_UPPER);

        if (!isset($rruleProps['DTSTART'])) {
            throw new SugarApiExceptionInvalidParameter('No DTSTART provided');
        }

        $dtStart = $rruleProps['DTSTART'];

        $rSet = new RSet();
        $rrule = new Rrule($givenRrule);

        $rSet->addRRule($rrule);

        $occurrences = $this->getOccurrences($rSet);

        $timezone = $dtStart->getTimezone();
        $utcTimezone = new DateTimeZone('UTC');
        $datetimeFormat = $timedate->get_date_time_format();

        foreach ($occurrences as $occurendIdx => $occurrence) {
            $occurrenceDate = SugarDateTime::createFromFormat($datetimeFormat, $occurrence, $timezone);
            $occurrences[$occurendIdx] = $occurrenceDate->setTimezone($utcTimezone)->format($datetimeFormat);
        }

        return $occurrences;
    }

    /**
     * Return the sugar params for recurrence from a Rrule
     * @param string $rrule
     * @return array
     */
    public function translateRRuleToSugarRecurrence(string $rrule): array
    {
        $rrule = RfcParser::parseRRule($rrule);
        $rrule = array_change_key_case($rrule, CASE_UPPER);

        if (!isset($rrule['DTSTART'])) {
            throw new SugarApiExceptionInvalidParameter('No DTSTART provided');
        }

        $dtStart = $rrule['DTSTART'];
        $utcTimezone = new DateTimeZone('UTC');
        $utcStartDate = $dtStart->setTimezone($utcTimezone)->format('Ymd\THis');

        $params = [];

        $params['sugarSupportedRrule'] = true;

        $unsupportedRrules = ['BYYEARDAY', 'BYWEEKNO', 'BYMINUTE', 'BYSECOND', 'BYEASTER', 'BYHOUR', 'WKST'];

        foreach ($unsupportedRrules as $unsupportedRrule) {
            if (array_key_exists($unsupportedRrule, $rrule)) {
                $params['sugarSupportedRrule'] = false;
                break;
            }
        }

        // Extract FREQ from RRULE and map it to repeat type
        switch ($rrule['FREQ']) {
            case 'DAILY':
                $params['repeat_type'] = 'Daily';
                break;
            case 'WEEKLY':
                $params['repeat_type'] = 'Weekly';
                break;
            case 'MONTHLY':
                $params['repeat_type'] = 'Monthly';
                break;
            case 'YEARLY':
                $params['repeat_type'] = 'Yearly';
                break;
            default:
                // Handle unsupported FREQ values
                $params['sugarSupportedRrule'] = false;
                break;
        }


        $startDate = SugarDateTime::createFromFormat('Ymd\THis', $utcStartDate);
        $startDateFormatted = $startDate->format('Y-m-d' . '\T' . 'H:i:s');

        $params['date_start'] = $startDateFormatted;

        if (isset($rrule['INTERVAL'])) {
            $params['repeat_interval'] = $rrule['INTERVAL'];
        }

        if (isset($rrule['COUNT'])) {
            $params['repeat_count'] = $rrule['COUNT'];
        } elseif (isset($rrule['UNTIL'])) {
            $repeatUntilDate = $rrule['UNTIL'];
            $repeatUntilFormatted = $repeatUntilDate->format('Y-m-d'.'\T'.'H:i:s');
            $params['repeat_until'] = $repeatUntilFormatted;
        } else {
            throw new SugarApiExceptionInvalidParameter('Infinite recurrence not supported, must provide until or count params');
        }

        // Extract BYDAY from RRULE and convert it to days of the week
        if (isset($rrule['BYDAY'])) {
            if ($rrule['FREQ'] === 'WEEKLY') { //BYDAY=MO,TH => repeat_dow => 14
                $params['repeat_dow'] = $this->convertByDayToRepeatDow($rrule['BYDAY']);
            } else {// BYDAY= 3MO => repeat_selector = On, repeat_unit = Mon; repeat_ordinal = third
                $rrule['BYSETPOS'] = $rrule['BYSETPOS'] ?? null; // special days like Weekend day, Week day
                $conversion = $this->convertByDayToRepeatUnitAndOrdinal($rrule['BYDAY'], $rrule['BYSETPOS']);

                $params['repeat_unit'] = $conversion['repeat_unit'];
                $params['repeat_ordinal'] = $conversion['repeat_ordinal'];
                $params['repeat_selector'] = 'On';
            }
        }

        if (isset($rrule['BYMONTHDAY'])) {
            $params = $this->convertByMonthDay($params, $rrule);
        } elseif (isset($rrule['BYMONTH'])) {
            $params['sugarSupportedRrule'] = false;
        }

        $unsupportedReturnParams = [
            'sugarSupportedRrule' => false,
            'date_start' => $params['date_start'],
        ];

        if (isset($params['repeat_type'])) {
            $unsupportedReturnParams['repeat_type'] = $params['repeat_type'];
        }

        if (!$params['sugarSupportedRrule']) {
            return $unsupportedReturnParams;
        }

        return $params;
    }

    /**
     * Converts the BYMONTHDAY rrule. It checks for special cases like first/second/third/fourth day of the year
     * and sets selector "on"
     * If not one of those, sets selector "each"
     * @param array $params
     * @param array $rrule
     * @return array  params
     */
    public function convertByMonthDay(array $params, array $rrule): array
    {
        $ordinalMap = [
            '1' => 'first',
            '2' => 'second',
            '3' => 'third',
            '4' => 'fourth',
        ];

        $firstMonthOfYear = '1';
        $lastMonthOfYear = '12';

        if ($rrule['FREQ'] === 'YEARLY' && isset($rrule['BYMONTH'])) {
            $params['repeat_selector'] = 'On';
            $params['repeat_unit'] = 'Day';

            if ($rrule['BYMONTH'] === $firstMonthOfYear && isset($ordinalMap[$rrule['BYMONTHDAY']])) {
                $params['repeat_ordinal'] = $ordinalMap[$rrule['BYMONTHDAY']];
            } elseif ($rrule['BYMONTH'] === $lastMonthOfYear && $rrule['BYMONTHDAY'] === '-1') {
                $params['repeat_ordinal'] = 'last';
            } else {
                $params['sugarSupportedRrule'] = false;
            }
        } else {
            $params['repeat_selector'] = 'Each';
            $params['repeat_days'] = $rrule['BYMONTHDAY'];
        }

        return $params['sugarSupportedRrule'] ?
            $params :
            [
                'sugarSupportedRrule' => $params['sugarSupportedRrule'],
                'date_start' => $params['date_start'],
                'repeat_type' => $params['repeat_type'],
            ];
    }

    /**
     * Convert the BYDAY string from RRULE to repeatDow format.
     *
     * @param string $byDay
     * @return string
     */
    public function convertByDayToRepeatDow(string $byDay): string
    {
        $dayMap = [
            'SU' => '0',
            'MO' => '1',
            'TU' => '2',
            'WE' => '3',
            'TH' => '4',
            'FR' => '5',
            'SA' => '6',
        ];

        $days = explode(',', $byDay);
        $repeatDow = '';

        foreach ($days as $day) {
            if (isset($dayMap[$day])) {
                $repeatDow .= $dayMap[$day];
            }
        }

        return $repeatDow;
    }

    /**
     * Convert the BYDAY string from RRULE to repeat_unit and repeat_ordinal
     * Takes into account the special units like Weekend and Week day set fro BYSETPOS
     *
     * @param string $byDay
     * @param string|null $BYSETPOS
     * @return array
     */
    public function convertByDayToRepeatUnitAndOrdinal(string $byDay, $BYSETPOS): array
    {
        $dayMap = [
            'SU' => 'Sun',
            'MO' => 'Mon',
            'TU' => 'Tue',
            'WE' => 'Wed',
            'TH' => 'Thu',
            'FR' => 'Fri',
            'SA' => 'Sat',
        ];

        $dayMapSpecial = [
            'MO,TU,WE,TH,FR' => 'WD',
            'SA,SU' => 'WE',
        ];

        $ordinal = '';
        $repeat_unit = '';

        if (isset($BYSETPOS)) {
            $repeat_unit = array_key_exists($byDay, $dayMapSpecial) ? $dayMapSpecial[$byDay] : '';
            $ordinal = $BYSETPOS;
        } else {
            preg_match('/(-?\d*)([A-Z]+)/', $byDay, $matches);// Extract the numeric part (ordinal) and the abbreviated day

            $ordinal = isset($matches[1]) ? $matches[1] : '';
            $abbreviatedDay = isset($matches[2]) ? $matches[2] : '';

            $repeat_unit = isset($dayMap[$abbreviatedDay]) ? $dayMap[$abbreviatedDay] : '';
        }

        switch ($ordinal) {
            case '-1':
                $ordinalValue = 'last';
                break;
            case '1':
                $ordinalValue = 'first';
                break;
            case '2':
                $ordinalValue = 'second';
                break;
            case '3':
                $ordinalValue = 'third';
                break;
            case '4':
                $ordinalValue = 'fourth';
                break;
            case '5':
                $ordinalValue = 'fifth';
                break;
            default:
                $ordinalValue = '';
        }

        return [
            'repeat_unit' => $repeat_unit,
            'repeat_ordinal' => $ordinalValue,
        ];
    }

    /**
     * Return the Rrule from a meetings params
     * @param array $event
     * @return array
     */
    public function translateSugarRecurrenceToRRule(array $event): array
    {
        $event = $this->adjustStartDate($event);

        $repeatType = isset($event['type']) ? $event['type'] : null;
        $repeatInterval = isset($event['interval']) ? $event['interval'] : null;
        $repeatCount = isset($event['count']) ? $event['count'] : null;
        $repeatUntil = isset($event['until']) ? $event['until'] : null ;
        $repeatDays = isset($event['days']) ? $event['days'] : null;
        $repeatDow = isset($event['dow']) ? $event['dow'] : null;
        $repeatOrdinal = isset($event['ordinal']) ? $event['ordinal'] : null;
        $repeatSelector = isset($event['selector']) ? $event['selector'] : null;
        $repeatUnit = isset($event['unit']) ? $event['unit'] : null;
        $dateStart = isset($event['start']) ? $this->formatDateTime('datetime', $event['start'], 'user') : null;

        $rrule = [
            'RRULE' => [
                'FREQ' => '',
            ],
            'DTSTART' => '',
        ];

        // Set the recurrence rule type based on the repeat type
        switch ($repeatType) {
            case 'Daily':
                $rrule['RRULE']['FREQ'] = 'DAILY';
                break;
            case 'Weekly':
                $rrule['RRULE']['FREQ'] = 'WEEKLY';
                break;
            case 'Monthly':
                $rrule['RRULE']['FREQ'] = 'MONTHLY';
                break;
            case 'Yearly':
                $rrule['RRULE']['FREQ'] = 'YEARLY';
                break;
            default:
                break;
        }

        global $timedate;
        global $current_user;

        $timezone = $current_user->getPreference('timezone');
        $timezone = $timezone ? $timezone : 'UTC';

        $userTimezone = new DateTimeZone($timezone);
        $startDate = SugarDateTime::createFromFormat($timedate->get_date_time_format(), $dateStart, $userTimezone);
        $dateStartFormatted = $startDate->format('Ymd\THis');

        $rrule['DTSTART'] = $dateStartFormatted;

        if (!empty($repeatInterval)) {
            $rrule['RRULE']['INTERVAL'] = $repeatInterval;
        }

        if (!empty($repeatCount)) {
            $rrule['RRULE']['COUNT'] = $repeatCount;
        } elseif (!empty($repeatUntil)) {
            $rrule['RRULE']['UNTIL'] = date('Ymd\T235959', strtotime($repeatUntil)) . 'Z';
        }

        if ($repeatType === 'Weekly' && !empty($repeatDow)) {
            $rrule['RRULE']['BYDAY'] = $this->convertRepeatDowToByDay($repeatDow);
        }

        if ($repeatSelector === 'None' && $repeatType === 'Monthly') {
            $dateTime = SugarDateTime::createFromFormat('Ymd\THis', $dateStartFormatted);
            $dayDateStart = ltrim($dateTime->format('d'), '0');
            $rrule['RRULE']['BYMONTHDAY'] = $dayDateStart;
        }

        if ($repeatSelector === 'Each') {
            $rrule['RRULE']['BYMONTHDAY'] = $repeatDays;
        }

        if ($repeatSelector === 'On' && !empty($repeatOrdinal) && !empty($repeatUnit)) {
            $rrule = $this->setOnRRule($rrule, $repeatOrdinal, $repeatUnit, $repeatType, $dateStartFormatted);
        }

        return $rrule;
    }

    /**
     * Converts the Days of Week string formed by integers to a string formed by characters EX:15 =>MO,FR
     * @param string $repeatDow
     * @return string
     */
    public function convertRepeatDowToByDay(string $repeatDow): string
    {
        $dayMap = [
            '0' => 'SU',
            '1' => 'MO',
            '2' => 'TU',
            '3' => 'WE',
            '4' => 'TH',
            '5' => 'FR',
            '6' => 'SA',
        ];

        $rruleByDay = [];

        $digits = str_split($repeatDow);

        foreach ($digits as $digit) {
            if (isset($dayMap[$digit])) {
                $rruleByDay[] = $dayMap[$digit];
            }
        }

        return implode(',', $rruleByDay);
    }

    /**
     * Return the Rrule for option Custom Date->On. It converts special units like Day, Weekday and Weekend Day to RRule vocabulary
     * @param array $rrule
     * @param string $repeatOrdinal
     * @param string $repeatUnit
     * @param string $repeatType
     * @param string $dateStartFormatted
     * @return array
     */
    public function setOnRRule(array $rrule, string $repeatOrdinal, string $repeatUnit, string $repeatType, string $dateStartFormatted): array
    {
        $ordinalMap = [ // Map repeatOrdinal values to their corresponding RRule values
            'first' => '1',
            'second' => '2',
            'third' => '3',
            'fourth' => '4',
            'fifth' => '5',
            'last' => '-1', // Use -1 for the last occurrence
        ];

        $unitMap = [  // Map repeatUnit values to their corresponding RRule values
            'Mon' => 'MO',
            'Tue' => 'TU',
            'Wed' => 'WE',
            'Thu' => 'TH',
            'Fri' => 'FR',
            'Sat' => 'SA',
            'Sun' => 'SU',
        ];

        $specialUnit = [  //special repeatUnit values that dont have corresponing RRule values by default
            'Day', //  the first/second/third/fourth/fifth day of month/year,
            'WD', //Monday -> Friday,
            'WE', //Saturday and Sunday
        ];

        $dateTime = SugarDateTime::createFromFormat('Ymd\THis', $dateStartFormatted);
        $monthDateStart = ltrim($dateTime->format('m'), '0');

        if (isset($ordinalMap[$repeatOrdinal])) { //regular unitMap functionality
            if (isset($unitMap[$repeatUnit])) { //first Mon => 1MO
                $rrule['RRULE']['BYDAY'] = $ordinalMap[$repeatOrdinal] . $unitMap[$repeatUnit];
                if ($repeatType === 'Yearly') {
                    $rrule['RRULE']['BYMONTH'] = $monthDateStart;
                }

                return $rrule;
            }

            if (in_array($repeatUnit, $specialUnit)) {//specialUnit for Day,Weekday and Weekend day
                if ($repeatUnit === 'Day') {
                    $firstMonth = '1';
                    $lastMonth = '12';
                    if ($repeatType === 'Monthly') {// first/second/third/fourth/fifth/last DAY of the Month
                        $rrule['RRULE']['BYMONTHDAY'] = $ordinalMap[$repeatOrdinal];

                        return $rrule;
                    }

                    if ($repeatType === 'Yearly') {// first/second/third/fourth/fifth/last DAY of the YEAR on Month January
                        $rrule['RRULE']['BYMONTHDAY'] = $ordinalMap[$repeatOrdinal];

                        if ($repeatOrdinal === 'last') {
                            $rrule['RRULE']['BYMONTH'] = $lastMonth;
                        } else {
                            $rrule['RRULE']['BYMONTH'] = $firstMonth;
                        }

                        return $rrule;
                    }
                }

                if ($repeatUnit === 'WD' || $repeatUnit === 'WE') {//Week day or weekend
                    if ($repeatType === 'Monthly' || $repeatType === 'Yearly') { // first/second/third/fourth/fifth/last MO/TU/WE/TH/FR OR SA/SU of the Month/YEAR
                        $rrule['RRULE']['BYDAY'] = ($repeatUnit === 'WD') ? 'MO,TU,WE,TH,FR' : 'SA,SU'; // EX: FREQ=MONTHLY;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=3;COUNT=10
                        $rrule['RRULE']['BYSETPOS'] = $ordinalMap[$repeatOrdinal];

                        return $rrule;
                    }
                }
            }
        }

        return $rrule;
    }

    /**
     * Gets the human readable string that we get from a rrule
     * @param string $rrule
     * @return string
     */
    public function getHumanReadableString(string $rruleString): string
    {
        global $current_user;
        global $sugar_config;

        $rrule = new Rrule($rruleString);

        $preferredLanguage = strtolower($current_user->preferred_language ?? $sugar_config['default_language']);

        $translatedLanguage = $this->DEFAULT_LANGUAGE;

        if (isset($this->RRULE_LANGUAGE_MAPPING_TO_SUGAR[$preferredLanguage])) {
            $translatedLanguage = $this->RRULE_LANGUAGE_MAPPING_TO_SUGAR[$preferredLanguage];
        } else {
            // Split the preferred language by underscores and check for short forms
            $languageParts = explode('_', $preferredLanguage);
            foreach ($languageParts as $languagePart) {
                $key = array_search($languagePart, $this->RRULE_LANGUAGE_MAPPING_TO_SUGAR);

                if ($key) {
                    $translatedLanguage = $key;
                    break;
                }
            }
        }

        $text = $rrule->humanReadable([
            'date_formatter' => function ($date) {
                return $date->format('n/j/y');
            },
            'locale' => $translatedLanguage,
        ]);

        return  ucfirst($text);
    }

    /**
     * Massages the params to fit the desired format for the translate functions
     * @param array $args
     * @return array $params
     */
    public function massageParams(array $args): array
    {
        $dateStart = $this->formatDateTime('datetime', $args['date_start'], 'user');

        $params = [];
        $params['type'] = $args['repeat_type'] ?? '';
        $params['interval'] = $args['repeat_interval'] ?? '';
        $params['count'] = $args['repeat_count'] ?? '';
        $params['until'] = $args['repeat_until'] ?? '';
        $params['dow'] = $args['repeat_dow'] ?? '';

        $params['selector'] = $args['repeat_selector'] ?? '';
        $params['days'] = $args['repeat_days'] ?? '';
        $params['ordinal'] = $args['repeat_ordinal'] ?? '';
        $params['unit'] = $args['repeat_unit'] ?? '';

        $params['start'] = $dateStart;

        return $params;
    }

    /**
     * Returns the rrule string made from params
     * @param array $args
     * @return string $rruleString
     */
    public function getRruleStringFromParams(array $args): string
    {
        $params = $this->massageParams($args);

        $rrule = $this->translateSugarRecurrenceToRRule($params);

        return $this->getRruleString($rrule);
    }

    /**
     * Calculate the first occurrence of the given weekday for a specific date.
     * @param string $date
     * @param int $weekday
     * @return string $formattedDateTime
     */
    public function findFirstWeekday(string $date, int $weekday): string
    {
        global $timedate;
        global $current_user;

        $dividerByWeek = 7;
        $timezone = $current_user->getPreference('timezone');
        $timezone = $timezone ? $timezone : 'UTC';

        $userTimezone = new DateTimeZone($timezone);

        $dateObj = SugarDateTime::createFromFormat($timedate->get_date_time_format(), $date, $userTimezone);

        $daysUntilWeekday = ($weekday - $dateObj->format('N') + $dividerByWeek) % $dividerByWeek;

        $dateObj->modify("+{$daysUntilWeekday} day");

        $formattedDateTime = $timedate->asUser($dateObj, $current_user);

        return $formattedDateTime;
    }

    /**
     * Map a repeat_dow string to the first next weekday from startdate.
     * @param string $repeat_dow
     * @param string $eventDate
     * @return int  the next Weekday
     */
    public function mapRepeatDowToFirstNextWeekday(string $repeat_dow, string $eventDate): int
    {
        global $timedate, $current_user;

        $targetDateTime = DateTime::createFromFormat($timedate->get_date_time_format($current_user), $eventDate);

        if (!$targetDateTime) {
            throw new SugarException('Invalid date format');
        }

        $timestamp = $targetDateTime->getTimestamp();

        $currentDayOfWeek = (int)date('N', $timestamp);
        $dowArray = array_map('intval', str_split($repeat_dow));

        foreach ($dowArray as $day) {
            if ($day >= $currentDayOfWeek) {
                return (int)$day;
            }
        }

        return $dowArray[0];
    }

    /**
     * Adjust the start date of an event that is Weekly based on the dow
     * @param array $event
     * @return array  $event The adjusted event data with the start date updated.
     */
    public function adjustStartDate(array $event): array
    {
        if (empty($event['days']) && $event['type'] === "Weekly") {
            $firstWeekday= $this->mapRepeatDowToFirstNextWeekday($event['dow'], $event['start']);

            $dateStartDow = $this->findFirstWeekday($event['start'], $firstWeekday);

            $event['start'] = $dateStartDow;
        }

        return $event;
    }
}
