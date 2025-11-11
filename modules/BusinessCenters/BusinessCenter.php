<?php

declare(strict_types=1);
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

/**
 * Class BusinessCenter
 */
class BusinessCenter extends Basic
{
    public $table_name = 'business_centers';
    public $module_name = 'BusinessCenters';
    public $module_dir = 'BusinessCenters';
    public $object_name = 'BusinessCenter';

    // Stored fields
    public $address_street;
    public $address_city;
    public $address_state;
    public $address_country;
    public $address_postalcode;
    public $timezone;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $team_name;
    public $team_id;

    public $is_open_sunday;
    public $sunday_open_hour;
    public $sunday_open_minutes;
    public $sunday_close_hour;
    public $sunday_close_minutes;
    public $is_open_monday;
    public $monday_open_hour;
    public $monday_open_minutes;
    public $monday_close_hour;
    public $monday_close_minutes;
    public $is_open_tuesday;
    public $tuesday_open_hour;
    public $tuesday_open_minutes;
    public $tuesday_close_hour;
    public $tuesday_close_minutes;
    public $is_open_wednesday;
    public $wednesday_open_hour;
    public $wednesday_open_minutes;
    public $wednesday_close_hour;
    public $wednesday_close_minutes;
    public $is_open_thursday;
    public $thursday_open_hour;
    public $thursday_open_minutes;
    public $thursday_close_hour;
    public $thursday_close_minutes;
    public $is_open_friday;
    public $friday_open_hour;
    public $friday_open_minutes;
    public $friday_close_hour;
    public $friday_close_minutes;
    public $is_open_saturday;
    public $saturday_open_hour;
    public $saturday_open_minutes;
    public $saturday_close_hour;
    public $saturday_close_minutes;

    // Pseudo fields filled in on retrieve
    public $sunday_open;
    public $sunday_close;
    public $monday_open;
    public $monday_close;
    public $tuesday_open;
    public $tuesday_close;
    public $wednesday_open;
    public $wednesday_close;
    public $thursday_open;
    public $thursday_close;
    public $friday_open;
    public $friday_close;
    public $saturday_open;
    public $saturday_close;

    protected $businessHours = [
        'open' => [
            'sunday' => '',
            'monday' => '',
            'tuesday' => '',
            'wednesday' => '',
            'thursday' => '',
            'friday' => '',
            'saturday' => '',
        ],
        'close' => [
            'sunday' => '',
            'monday' => '',
            'tuesday' => '',
            'wednesday' => '',
            'thursday' => '',
            'friday' => '',
            'saturday' => '',
        ],
    ];

    protected $dayMap = [
        'su' => 'sunday',
        'm' => 'monday',
        't' => 'tuesday',
        'w' => 'wednesday',
        'th' => 'thursday',
        'f' => 'friday',
        's' => 'saturday',
    ];

    public $importable = true;

    protected $timeBases = [
        'open' => [
            'hour' => '00',
            'minutes' => '00',
        ],
        'close' => [
            'hour' => '23',
            'minutes' => '59',
        ],
    ];

    /**
     * A list of related holidays
     * @var array
     */
    protected $holidays = [];

    /**
     * Determines whether we need to recollect the holidays for this business
     * center
     * @var string
     */
    protected $holidaysCacheKey = '';

    /**
     * Mapping of supported interval units to methods and expected types for each
     * @var array
     */
    protected $intervalUnits = [
        'hours' => [
            'method' => 'getBusinessDatetimeFromInterval',
            'type' => 'float',
        ],
        'days' => [
            'method' => 'getBusinessDateFromInterval',
            'type' => 'int',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * Gets the time prop for a day. This will be either open or close for a day
     * of the week, so something like `wednesday_open` or `sunday_close`.
     *
     * This method expects that `$day` has already been normalized.
     *
     * @param string $day Day of the week, or shortcode
     * @param string $type `open` or `close`
     * @return string
     */
    protected function getTimeProp(string $day, string $type)
    {
        return sprintf('%s_%s', $day, $type);
    }

    /**
     * Sets the open and close times for a day
     * @param string $day Day name or shortcode for a day of the week
     */
    public function setDayDefaults($day)
    {
        if ($this->isOpen($day)) {
            // Needed for the assembly of the day property below
            $day = $this->getNormalizedDay($day);
            foreach ($this->timeBases as $type => $data) {
                foreach ($data as $time => $value) {
                    $prop = sprintf('%s_%s_%s', $day, $type, $time);
                    if (!isset($this->$prop)) {
                        $this->$prop = $value;
                    }
                }

                $prop = $this->getTimeProp($day, $type);
                $this->$prop = $this->{$prop . '_hour'} . $this->{$prop . '_minutes'};
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fill_in_additional_detail_fields()
    {
        foreach ($this->dayMap as $day) {
            $this->setDayDefaults($day);
        }
    }

    /**
     * Checks to see if this business center has any business hours set up.
     * @return boolean
     */
    public function hasBusinessHours()
    {
        foreach ($this->dayMap as $day) {
            if ($this->isOpen($day)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets a normalized name of a day
     * @param string $day Either the string name of a day or a mapped shortcut
     * @return string
     */
    public function getNormalizedDay(string $day)
    {
        $day = strtolower($day);
        if (!in_array($day, $this->dayMap)) {
            if (!isset($this->dayMap[$day])) {
                return false;
            }

            $day = $this->dayMap[$day];
        }

        return $day;
    }

    /**
     * Checks if this business center is open on a given day of the week
     * @param string $day Either the string name of a day or a mapped shortcut
     * @return boolean
     */
    public function isOpen(string $day)
    {
        $field = 'is_open_' . $this->getNormalizedDay($day);
        return (bool)$this->$field;
    }

    /**
     * Gets the open time for a day for this business center
     * @param string $day Day of the week, or shortcode
     * @return string|null
     */
    public function getOpenTime(string $day)
    {
        return $this->getTimeForTypeOnDay($day, 'open');
    }

    /**
     * Gets the close time for a day for this business center
     * @param string $day Day of the week, or shortcode
     * @return string|null
     */
    public function getCloseTime(string $day)
    {
        return $this->getTimeForTypeOnDay($day, 'close');
    }

    /**
     * Gets the open or close time for a day for this business center
     * @param string $day Day of the week, or shortcode
     * @param string $type `open` or `close`
     * @param bool $asParts Whether to return the hours and minutes as array
     * @return string[]|string|null
     */
    public function getTimeForTypeOnDay(string $day, string $type, bool $asParts = false)
    {
        $type = trim(strtolower($type));
        if ($type !== 'open' && $type !== 'close') {
            return null;
        }

        $day = $this->getNormalizedDay($day);
        $prop = $this->getTimeProp($day, $type);

        // If we want the parts, send those back, otherwise send back the
        // assembled string property
        return $asParts ?
            [
                'hour' => (int)$this->{$prop . '_hour'},
                'minutes' => (int)$this->{$prop . '_minutes'},
            ] :
            $this->$prop;
    }

    /**
     * Gets the list of hours for use in the hours portion of a time
     * @return array
     */
    public function getHoursDropdown()
    {
        return $this->getTimeUnitDropdown('hours');
    }

    /**
     * Gets the list of minutes for use in the minutes portion of a time
     * @return array
     */
    public function getMinutesDropdown()
    {
        return $this->getTimeUnitDropdown('minutes');
    }

    /**
     * Gets a list of hours or minutes for use in time dropdowns, based on type
     * @param string $unit Type of unit to get: `minutes` or `hours`
     * @return array
     */
    public function getTimeUnitDropdown(string $unit)
    {
        if ($unit !== 'minutes' && $unit !== 'hours') {
            return [];
        }

        // Set the loop limit to either 23 for hours or 59 for minutes
        $cap = $unit === 'hours' ? 23 : 59;

        // Prepare the response
        $r = [];
        for ($i = 0; $i <= $cap; $i++) {
            // Turn the numbers into 2space padded string representations: 00-23
            $r[$i] = str_pad("$i", 2, '0', STR_PAD_LEFT);
        }

        // Return it
        return $r;
    }

    /**
     * Gets the number of hours on a day that a business center is open
     * @param string $day Day of the week, or shortcode
     * @return float
     */
    public function getHoursOpenForDay(string $day)
    {
        if (!$this->isOpen($day)) {
            return 0.00;
        }

        $day = $this->getNormalizedDay($day);

        // Hours are easiest, so start with those
        $hoursDiff = (int)$this->{$day . '_close_hour'} - (int)$this->{$day . '_open_hour'};

        // Handle close minutes
        $cm = (int)$this->{$day . '_close_minutes'};

        // If close is 59 then move the close to 0 and add an hour
        if ($cm === 59) {
            $cm = 0;
            $hoursDiff += 1;
        }

        $om = (int)$this->{$day . '_open_minutes'};

        // If the open minutes are greater than the close minutes, adjust the
        // hours and minutes to account for it
        if ($om > $cm) {
            // Reduce the hours by one
            $hoursDiff -= 1;

            // And add an hours worth of minutes
            $cm += 60;
        }

        // Get a 2 precision rounded float value for the diff
        return round($hoursDiff + (($cm - $om) / 60), 2);
    }

    /**
     * Gets a datetime stamp for the close of business after `$interval` number
     * of days from a date given as a SugarDateTime object
     * @param \SugarDateTime $sdt Datetime object that contains the from date
     * @param int|integer $interval Number of days to use in the calculation
     * @return string
     */
    protected function getBusinessDateFromInterval(\SugarDateTime $sdt, int $interval = 1)
    {
        // Needed for tracking if we should increment the date the first time through
        $first = true;

        while (true) {
            // If we are open handle incrementing from here, otherwise move to
            // the next day and try again
            if ($this->isOpenDate($sdt, true)) {
                // If there are days left, decrement and move on
                if ($interval > 0) {
                    // Since a business day is always at least one day, if
                    // this is the first time through and the date is open, move
                    // to the next day
                    if ($first) {
                        $sdt = $this->setDateToNextOpenTime($sdt);
                        $first = false;
                    }

                    // Knock one day off the interval
                    $interval--;
                }

                // When we reach no more days left in the interval, set the date
                // to the closed time for that day and send the datetime back
                if ($interval === 0) {
                    $sdt = $this->setCloseTimeOnDate($sdt);
                    return $sdt->format(DateTimeInterface::ATOM);
                }
            } else {
                // First pass was not open, so mark that we have handled the first
                // day and move on
                if ($first) {
                    $first = false;
                }
            }

            // Move to the next day
            $sdt = $this->setDateToNextOpenTime($sdt);
        }
    }

    /**
     * Gets a datetime stamp for the close of business after `$interval` number
     * of hours from a datetime given as a SugarDateTime object
     * @param \SugarDateTime $sdt Datetime object that contains the from date and time
     * @param float $interval Number of hours to use in the calculation
     * @return string
     */
    protected function getBusinessDatetimeFromInterval(\SugarDateTime $sdt, float $interval = 0.1)
    {
        while (true) {
            // If we are open, specifically at this time, start working
            if ($this->isOpenDate($sdt, true)) {
                $timeLeftInDay = $this->getTimeLeftInDay($sdt);

                // If the time left in the day is more than the interval, add the
                // interval to the date object and send it back, since that means we
                // are covered for today
                if ($timeLeftInDay >= $interval) {
                    $hours = intval($interval);
                    $mins = intval(($interval - $hours) * 60);
                    $sdt = $sdt->get("$hours hours $mins minutes");

                    return $sdt->format(DateTimeInterface::ATOM);
                }

                // Decrement the interval by time left in the day and recalculate
                $interval -= $timeLeftInDay;
            }

            $sdt = $this->setDateToNextOpenTime($sdt);
        }
    }

    /**
     * Determines if a calculation for incremented time can be done on this
     * business center based on hours of operation, interval and unit.
     *
     * In order for a business center to be able to calculate an incremented
     * timestamp the business center must:
     *  - Have at least one day of business hours
     *  - Have an `interval` that is greater than 0
     *  - Support the `unit` for the increment: hours or days
     *
     * @param float|int $interval Interval of hours
     * @param string $unit For now we only support hours
     * @return boolean
     */
    public function canCalculateIncrement($interval = 0, $unit = 'hours')
    {
        return $this->hasBusinessHours() &&
            floatval($interval) > 0.0 &&
            isset($this->intervalUnits[$unit]);
    }

    /**
     * Gets a DB date time string of the date that is `$interval` hours greater
     * than `$datetime`
     * @param string $datetime A date string
     * @param float|int $interval Interval of hours
     * @param string $unit For now we only support hours
     * @return string DB Formatted date string
     */
    public function getIncrementedBusinessDatetime(string $datetime, $interval = 0, $unit = 'hours')
    {
        // Everything hinges on a DateTime object, so set that up straight away
        $sdt = new \SugarDateTime(
            $datetime,
            empty($this->timezone) ? null : new \DateTimeZone($this->timezone)
        );

        // If we receive a unit we do not expect, or we get a 0 value interval,
        // or if this business center does not have business hours setup then send
        // back what was given
        if ($this->canCalculateIncrement($interval, $unit) === false) {
            return $sdt->format(DateTimeInterface::ATOM);
        }

        // Typing is important here
        settype($interval, $this->intervalUnits[$unit]['type']);
        return $this->{$this->intervalUnits[$unit]['method']}($sdt, $interval);
    }

    /**
     * Gets the remaining time left for a business center on a given date
     * @param \SugarDateTime $sdt The configured SugarDateTime object
     * @return float
     */
    protected function getTimeLeftInDay(\SugarDateTime $sdt)
    {
        // How many hours is this bc open from the presented date
        $c = $this->getCloseTimeElements($sdt->day_of_week_english);
        $ch = $c['hour'];
        $cm = $c['minutes'];

        // Subtract the remaining hours from the interval
        $dh = $sdt->getHour();
        $dm = $sdt->getMinute();

        // Hours are easiest, so start with those
        $hoursDiff = $ch - $dh;

        // If close is 59 then move the close to 0 and add an hour
        if ($cm === 59) {
            $cm = 0;
            $hoursDiff += 1;
        }

        // If the date minutes are greater than the close minutes, adjust the
        // hours and minutes to account for it
        if ($dm > $cm) {
            // Reduce the hours by one
            $hoursDiff -= 1;

            // And add an hours worth of minutes to the close
            $cm += 60;
        }

        // Get a 2 precision rounded float value for the diff
        return round($hoursDiff + (($cm - $dm) / 60), 2);
    }

    /**
     * Sets the date object used in calculations to the next opening day and
     * time for the Business Center
     * @param \SugarDateTime $sdt
     * @return SugarDateTime
     */
    protected function setDateToNextOpenTime(\SugarDateTime $sdt, bool $checkTime = false)
    {
        // See if datetime is before before opening on this date
        $next = $this->setOpenTimeOnDate(clone $sdt);
        if ($next > $sdt) {
            return $next;
        }

        // Get the next open business day
        $sdt = $this->getNextOpenDay($sdt->get('tomorrow'), $checkTime);

        return $this->setOpenTimeOnDate($sdt);
    }

    /**
     * Sets the open time for a business center on a date object
     * @param \SugarDateTime $sdt SugarDateTime object
     * @return SugarDateTime A date time object with the open time set for this business center
     */
    protected function setOpenTimeOnDate(\SugarDateTime $sdt)
    {
        return $this->setTimeOnDate($sdt, 'open');
    }

    /**
     * Sets the close time for a business center on a date object
     * @param \SugarDateTime $date The date time object
     * @return SugarDateTime A date time object with the closed time set for this business center
     */
    protected function setCloseTimeOnDate(\SugarDateTime $sdt)
    {
        return $this->setTimeOnDate($sdt, 'close');
    }

    /**
     * Sets the open or close time for a business center on a date object
     * @param \SugarDateTime $date The date time object
     * @return SugarDateTime A date time object with the proper time set for this business center
     */
    protected function setTimeOnDate(\SugarDateTime $sdt, $type)
    {
        if ($type !== 'open' && $type !== 'close') {
            return $sdt;
        }

        // Not entirely necessary to mutate the string name for the method, but
        // it feels right
        $method = 'get' . ucfirst(strtolower($type)) . 'TimeElements';
        $e = $this->$method($sdt->day_of_week_english);

        // Set the time stamp on our date object to the close time
        $date = clone $sdt;
        $date->setTime($e['hour'], $e['minutes']);

        return $date;
    }

    /**
     * Checks openness of a day/date and returns the next open date
     * @param SugarDateTime $sdt SugarDateTime object
     * @param bool $checkTime Flag that tells downstream methods whether to account for time on a date
     * @return SugarDateTime Modified SugarDateTime object if the next open day
     */
    protected function getNextOpenDay(\SugarDateTime $sdt, bool $checkTime = false)
    {
        while ($this->isOpenDate($sdt, $checkTime) === false) {
            $sdt = $sdt->get('tomorrow');
        }

        return $sdt;
    }

    /**
     * Gets the list of related holidays for this business center
     * @param SugarDateTime $fromDate DateTime object for the from date
     * @param SugarDateTime|null $toDate DateTime object for the to date
     * @param bool $fresh Cache clearing indicator
     * @return array
     */
    public function getHolidays(\SugarDateTime $fromDate, \SugarDateTime $toDate = null, bool $fresh = false)
    {
        // Used for both the from in collection and caching
        $from = (clone $fromDate)->modify('first day of this month')->format('Y-m-d');

        // If we have the related holiday list already, send it back
        if (empty($this->holidays) || $fresh || $this->holidaysCacheKey !== $from) {
            // Reset the internal cache
            $this->holidays = [];

            // Reset the cache key
            $this->holidaysCacheKey = $from;

            // If we can't load the relationship then assume there are no related
            // holidays
            if (!$this->load_relationship('business_holidays')) {
                return [];
            }

            // If there is no toDate then set one for one year later
            if ($toDate === null) {
                $toDate = $fromDate->get('1 year');
            }

            // Get our to for the between parameters
            $to = (clone $toDate)->modify('last day of this month')->format('Y-m-d');

            $holidays = $this->business_holidays->getBeans([
                'where' => [
                    'lhs_field' => 'holiday_date',
                    'operator' => 'BETWEEN',
                    'rhs_value' => ['min' => $from, 'max' => $to],
                ],
            ]);

            foreach ($holidays as $holiday) {
                $sdt = new \SugarDateTime($holiday->holiday_date);
                $this->holidays[$sdt->format('Y-m-d')] = $holiday->name;
            }
        }

        return $this->holidays;
    }

    /**
     * Gets the hours open for an actual date
     * @param SugarDateTime $sdt Configured SugarDateTime object
     * @return float
     */
    protected function getHoursOpenForDate(\SugarDateTime $sdt)
    {
        if ($this->isOpenDate($sdt)) {
            return $this->getHoursOpenForDay($sdt->day_of_week_english);
        }

        return 0.00;
    }

    /**
     * Checks if this business center is open on a given date
     * @param SugarDateTime $date SugarDateTime object
     * @param bool $checkTime If true, will check the time on the date object too
     * @return boolean
     */
    protected function isOpenDate(\SugarDateTime $date, bool $checkTime = false)
    {
        // First check the day of the week, since that is simple
        $day = $date->day_of_week_english;
        if (!$this->isOpen($day)) {
            return false;
        }

        // Now get the related holidays for this business center and see if the
        // date is a holiday
        $checkDate = $date->format('Y-m-d');
        $holidays = $this->getHolidays($date);
        if (isset($holidays[$checkDate])) {
            return false;
        }

        if ($checkTime) {
            // Now check the time for this business center against the time on the
            // date object
            $open = $this->setOpenTimeOnDate(clone $date);
            $close = $this->setCloseTimeOnDate($date);

            // If the date is the same or bigger than open, and the same or less
            // than close then it is open
            return $open <= $date && $date <= $close;
        }

        return true;
    }

    /**
     * Gets the open time elements for a day for this business center
     * @param string $day Day of the week, or shortcode
     * @return string[]|null
     */
    public function getOpenTimeElements(string $day)
    {
        return $this->getTimeForTypeOnDay($day, 'open', true);
    }

    /**
     * Gets the close time elements for a day for this business center
     * @param string $day Day of the week, or shortcode
     * @return string[]|null
     */
    public function getCloseTimeElements(string $day)
    {
        return $this->getTimeForTypeOnDay($day, 'close', true);
    }

    /**
     * Calculates the total business time (in decimal hours now) between the two given datetimes,
     * based on this business center's hours.
     *
     * @param SugarDateTime $startDateTime The start date
     * @param SugarDateTime $endDateTime The end date
     * @return float The total business time (in decimal)
     */
    public function getBusinessTimeBetween(\SugarDateTime $startDateTime, \SugarDateTime $endDateTime)
    {
        $businessSeconds = 0;

        if ($startDateTime >= $endDateTime) {
            return 0.00;
        }

        if ($this->timezone) {
            // convert to business center time zone
            $startDateTime->setTimeZone(new DateTimeZone($this->timezone));
            $endDateTime->setTimeZone(new DateTimeZone($this->timezone));
        } else {
            // convert to server time zone
            $startDateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
            $endDateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        }

        while (true) {
            // If the date is past the end date, format and return the accumulated results
            if ($startDateTime >= $endDateTime) {
                return $this->secondsToHours($businessSeconds);
            } elseif ($this->isOpenDate($startDateTime, true)) {
                // Get the amount of time left until the end of the business day (in seconds)
                $secondsLeftInDay = $this->getSecondsLeftInDay($startDateTime);

                // Move the date to the end of the current working day
                $startDateTime->add(new DateInterval('PT' . $secondsLeftInDay . 'S'));

                // If the date is now past the end date, that means the end datetime occurs during this working day.
                if ($startDateTime >= $endDateTime) {
                    // Calculate the seconds until the end date
                    $secondsLeftUntilEnd = $secondsLeftInDay - $this->getSecondsLeftInDay($endDateTime);
                    $businessSeconds += $secondsLeftUntilEnd;
                    return $this->secondsToHours($businessSeconds);
                }
                $businessSeconds += $secondsLeftInDay;
            }

            // Move the date to the next open datetime from business center
            $startDateTime = $this->setDateToNextOpenTime($startDateTime);
        }
    }

    /**
     * Converts seconds to decimal hours
     * @param int $seconds The seconds need to be converted to hours
     * @param int $precision The precision of the output hours
     * @return float A rounded up decimal hours
     */
    protected function secondsToHours(int $seconds, int $precision = 2)
    {
        return round($seconds / 3600, $precision);
    }

    /**
     * The time left open for a business center on a given date
     * @param SugarDateTime $dateTime
     * @return int the number of seconds left in the current working day
     */
    protected function getSecondsLeftInDay(\SugarDateTime $dateTime)
    {
        $closeTime = $this->getCloseTimeElements($dateTime->day_of_week_english);

        // Get the second of the day that closing occurs
        $closeSecond = ($closeTime['hour'] * 3600) + ($closeTime['minutes'] * 60);

        // Get the current second of the day
        $currentSecond = ($dateTime->getHour() * 3600) + ($dateTime->getMinute() * 60) + $dateTime->getSecond();

        return $closeSecond - $currentSecond;
    }
}
