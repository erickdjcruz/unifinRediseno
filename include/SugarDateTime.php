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

/**
 * Sugar DateTime container
 * Extends regular PHP DateTime with useful services
 * @api
 */
class SugarDateTime extends DateTime
{
    public const DOW_SUN = 0;
    public const DOW_MON = 1;
    public const DOW_TUE = 2;
    public const DOW_WED = 3;
    public const DOW_THU = 4;
    public const DOW_FRI = 5;
    public const DOW_SAT = 6;

    /**
     * @var array
     */
    public $def;
    /**
     * @var bool
     */
    public $isDate;

    // Recognized properties and their formats
    protected $formats = [
        'sec' => 's',
        'min' => 'i',
        'hour' => 'G',
        'zhour' => 'H',
        'day' => 'j',
        'zday' => 'd',
        'days_in_month' => 't',
        'day_of_week' => 'w',
        'day_of_year' => 'z',
        'week' => 'W',
        'month' => 'n',
        'zmonth' => 'm',
        'year' => 'Y',
        'am_pm' => 'A',
        'hour_12' => 'g',
    ];

    // Property aliases
    protected $var_gets = [
        '24_hour' => 'hour',
        'day_of_week' => 'day_of_week_long',
        'day_of_week_short' => 'day_of_week_short',
        'month_name' => 'month_long',
        'hour' => 'hour_12',
    ];

    /**
     * @var DateTimeZone
     */
    // @codingStandardsIgnoreLine PSR2.Classes.PropertyDeclaration.Underscore
    protected static $_gmt;

    /**
     * English Language weekdays. These are needed for datetime-aware modules
     * (e.g. Business Centers) that use english language weekdays as field
     * names.
     *
     * @var array
     */
    protected $englishWeekdays = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];

    /**
     * Calendar strings
     * @var array
     */
    protected $_strings;

    /**
     * For testing - if we allowed to use PHP date parse
     * @var bool
     */
    public static $use_php_parser = true;

    /**
     * Copy of DateTime::createFromFormat
     *
     * Needed to return right type of the object
     *
     * @param string $format Format like in date()
     * @param string $time Time to parse
     * @param DateTimeZone $timezone
     * @return SugarDateTime|bool The created SugarDateTime object,
     *   or false on failure.
     * @see DateTime::createFromFormat
     */
    #[\ReturnTypeWillChange]
    public static function createFromFormat($format, $time, $timezone = null)
    {
        if (empty($time) || empty($format)) {
            return false;
        }
        if (!empty($timezone)) {
            $d = parent::createFromFormat($format, $time, $timezone);
        } else {
            $d = parent::createFromFormat($format, $time);
        }
        if (!$d) {
            return false;
        }
        $sd = new self($d->format(DateTimeInterface::ISO8601));
        $sd->setTimezone($d->getTimezone());
        return $sd;
    }

    /**
     * Load language Calendar strings
     * @param string $name string section to return
     * @return array
     * @internal
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getStrings($name)
    {
        if (empty($this->_strings)) {
            $this->_strings = return_mod_list_strings_language($GLOBALS['current_language'], 'Calendar');
        }
        return $this->_strings[$name];
    }

    /**
     * Fetch property of the date by name
     * @param string $var Property name
     * @return mixed
     */
    public function __get($var)
    {
        // simple formats
        if (isset($this->formats[$var])) {
            return $this->format($this->formats[$var]);
        }
        // conditional, derived and translated ones
        switch ($var) {
            case 'ts':
                return $this->format('U') + 0;
            case 'tz_offset':
                return $this->getTimezone()->getOffset($this);
            case 'days_in_year':
                return $this->format('L') == '1' ? 366 : 365;
                break;
            case 'day_of_week_short':
                $str = $this->_getStrings('dom_cal_weekdays');
                return $str[$this->day_of_week];
            case 'day_of_week_long':
                $str = $this->_getStrings('dom_cal_weekdays_long');
                return $str[$this->day_of_week];
            case 'day_of_week_english':
                $str = $this->englishWeekdays;
                return $str[$this->day_of_week];
            case 'month_short':
                $str = $this->_getStrings('dom_cal_month');
                return $str[$this->month];
            case 'month_long':
                $str = $this->_getStrings('dom_cal_month_long');
                return $str[$this->month];
        }

        return '';
    }

    /**
     * Implement some get_ methods that fetch variables
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        // fill in 5.2.x gaps
        if ($name == 'getTimestamp') {
            return $this->format('U') + 0;
        }
        if ($name == 'setTimestamp') {
            $sec = (int)$args[0];
            $sd = new self("@$sec");
            $sd->setTimezone($this->getTimezone());
            return $sd;
        }

        // getters
        if (substr($name, 0, 4) == 'get_') {
            $var = substr($name, 4);

            if (isset($this->var_gets[$var])) {
                return $this->__get($this->var_gets[$var]);
            }

            if (isset($this->formats[$var])) {
                return $this->__get($var);
            }
        }
        $GLOBALS['log']->fatal("SugarDateTime: unknowm method $name called");
        sugar_die("SugarDateTime: unknowm method $name called");
        return false;
    }

    /**
     * Get specific hour of today
     * @param int $hour_index
     * @return SugarDateTime
     */
    public function get_datetime_by_index_today($hour_index)
    {
        if ($hour_index < 0 || $hour_index > 23) {
            sugar_die('hour is outside of range');
        }

        $newdate = clone $this;
        $newdate->setTime($hour_index, 0, 0);
        return $newdate;
    }

    /**
     * Get the last second of current hour
     * @return SugarDateTime
     */
    public function get_hour_end_time()
    {
        $newdate = clone $this;
        $newdate->setTime($this->hour, 59, 59);
        return $newdate;
    }

    /**
     * Get the last second of the current day
     * @return SugarDateTime
     */
    public function get_day_end_time()
    {
        $newdate = clone $this;
        return $newdate->setTime(23, 59, 59);
    }

    /**
     * Get the beginning of i's day of the week
     * @param int $day_index Day, 0 is Sunday, 1 is Monday, etc.
     * @return SugarDateTime
     */
    public function get_day_by_index_this_week($day_index)
    {
        $newdate = clone $this;
        $newdate->setDate($this->year, $this->month, $this->day +
            ($day_index - $this->day_of_week))->setTime(0, 0);
        return $newdate;
    }

    /**
     * Get the beginning of the last day of i's the month
     * @param int $month_index Month, January is 0
     * @return SugarDateTime
     * @deprecated
     * FIXME: no idea why this function exists and what's the use of it
     */
    public function get_day_by_index_this_year($month_index)
    {
        $newdate = clone $this;
        $newdate->setDate($this->year, $month_index + 1, 1);
        $newdate->setDate($newdate->year, $newdate->month, $newdate->days_in_month);
        $newdate->setTime(0, 0);
        return $newdate;
    }

    /**
     * Get the beginning of i's day of the month
     * @param int $day_index 0 is the first day of the month (sic!)
     * @return SugarDateTime
     */
    public function get_day_by_index_this_month($day_index)
    {
        $newdate = clone $this;
        return $newdate->setDate($this->year, $this->month, $day_index + 1)->setTime(0, 0);
    }

    /**
     * Get new date, modified by date expression
     *
     * @param string $expression
     * @return SugarDateTime
     * @example $yesterday = $today->get("yesterday");
     *
     */
    public function get($expression)
    {
        $newdate = clone $this;
        $newdate->modify($expression);
        return $newdate;
    }

    /**
     * Create from ISO 8601 datetime
     * @param string $str
     * @return SugarDateTime
     */
    public static function parse_utc_date_time($str)
    {
        return new self($str);
    }

    /**
     * Create a list of time slots for calendar view
     * Times must be in user TZ
     * @param string $view Which view we are using - day, week, month
     * @param SugarDateTime $start_time Start time
     * @param SugarDateTime $end_time End time
     * @return array
     */
    public static function getHashList($view, $start_time, $end_time)
    {
        $hash_list = [];

        if ($view != 'day') {
            $end_time = $end_time->get_day_end_time();
        }

        $end = $end_time->ts;
        if ($end <= $start_time->ts) {
            $end = $start_time->ts + 1;
        }

        $new_time = clone $start_time;
        $new_time->setTime($new_time->hour, 0, 0);

        while ($new_time->ts < $end) {
            if ($view == 'day') {
                $hash_list[] = $new_time->format(TimeDate::DB_DATE_FORMAT) . ':' . $new_time->hour;
                $new_time->modify('next hour');
            } else {
                $hash_list[] = $new_time->format(TimeDate::DB_DATE_FORMAT);
                $new_time->modify('next day');
            }
        }

        return $hash_list;
    }

    /**
     * Get the beginning of the given day
     * @param int $day Day, starting with 1, default is current
     * @param int $month Month, starting with 1, default is current
     * @param int $year Year, default is current
     * @return SugarDateTime
     */
    public function get_day_begin($day = null, $month = null, $year = null)
    {
        $newdate = clone $this;
        $newdate->setDate(
            $year ?: $this->year,
            $month ?: $this->month,
            $day ?: $this->day
        );
        $newdate->setTime(0, 0);
        return $newdate;
    }

    /**
     * Get the last second of the given day
     * @param int $day Day, starting with 1, default is current
     * @param int $month Month, starting with 1, default is current
     * @param int $year Year, default is current
     * @return SugarDateTime
     */
    public function get_day_end($day = null, $month = null, $year = null)
    {
        $newdate = clone $this;
        $newdate->setDate(
            $year ?: $this->year,
            $month ?: $this->month,
            $day ?: $this->day
        );
        $newdate->setTime(23, 59, 59);
        return $newdate;
    }

    /**
     * Get the beginning of the first day of the year
     * @param int $year
     * @return SugarDateTime
     */
    public function get_year_begin($year)
    {
        $newdate = clone $this;
        $newdate->setDate($year, 1, 1);
        $newdate->setTime(0, 0);
        return $newdate;
    }

    /**
     * Print datetime in standard DB format
     *
     * Set $tz parameter to false if you are sure that the date is in UTC.
     *
     * @param bool $tz do conversion to UTC
     * @return string
     */
    public function asDb($tz = true)
    {
        if ($tz) {
            if (empty(self::$_gmt)) {
                self::$_gmt = new DateTimeZone('UTC');
            }
            $this->setTimezone(self::$_gmt);
        }
        return $this->format(TimeDate::DB_DATETIME_FORMAT);
    }

    /**
     * Print date in standard DB format
     *
     * Set $tz parameter to false in order to prevent timezone conversion to UTC.
     *
     * @param bool $tz do conversion to UTC
     * @return string
     */
    public function asDbDate($tz = true)
    {
        if ($tz) {
            if (empty(self::$_gmt)) {
                self::$_gmt = new DateTimeZone('UTC');
            }
            $this->setTimezone(self::$_gmt);
        }
        return $this->format(TimeDate::DB_DATE_FORMAT);
    }

    /**
     * Get query string for the date, year=%d&month=%d&day=%d&hour=%d
     * @return string
     */
    public function get_date_str()
    {
        return sprintf('&year=%d&month=%d&day=%d&hour=%d', $this->year, $this->month, $this->day, $this->hour);
    }

    /**
     * Convert date to string - 'r' format, like: Thu, 21 Dec 2000 16:01:07 +0200
     * @return string
     */
    public function __toString(): string
    {
        return $this->format('r');
    }

    // 5.2 compatibility - 5.2 functions don't return $this, let's help them
    // leave it as is because of different return on error: false on DateTime, $this on SugarDateTime

    /**
     * (non-PHPdoc)
     * @param $year
     * @param $month
     * @param $day
     * @return SugarDateTime
     * @see DateTime::setDate()
     */
    public function setDate($year, $month, $day): SugarDateTime
    {
        parent::setDate($year, $month, $day);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @param $hour
     * @param $minute
     * @param int $sec
     * @return SugarDateTime
     * @see DateTime::setTime()
     */
    public function setTime($hour, $minute, $sec = 0, $microseconds = 0): SugarDateTime
    {
        parent::setTime((int)$hour, (int)$minute, (int)$sec);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @param $modify
     * @return SugarDateTime
     * @see DateTime::modify()
     */
    #[\ReturnTypeWillChange]
    public function modify($modify)
    {
        parent::modify($modify);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @param DateTimeZone $timezone
     * @return SugarDateTime
     * @see DateTime::setTimezone()
     */
    public function setTimezone($timezone): SugarDateTime
    {
        if (!$timezone instanceof \DateTimeZone) {
            LoggerManager::getLogger()->warn(
                sprintf('DateTime::setTimezone() expects parameter 1 to be DateTimeZone, %s given', gettype($timezone)) .  PHP_EOL . (new Exception())->getTraceAsString()
            );
            return $this;
        }
        parent::setTimezone($timezone);
        return $this;
    }

    /**
     * Takes this date time and shuffles it back by the requested offset.
     * This is a 5.2 compatibility chunk, strptime()'s handling of the ISO
     *   is unpredictable enough that this is more reliable
     * @param string $isoOffset
     * @return SugarDateTime
     */
    public function adjustByIsoOffset($isoOffset)
    {
        if ($isoOffset == 'Z' || $isoOffset == '-0000' || $isoOffset == '+0000') {
            // It's GMT, so that's... 0 seconds from GMT.
            $calcOffset = 0;
            return $this;
        } else {
            // This will turn into (int)-1 or +1, useful for multiplying out the seconds
            $plusMinus = (int)(substr($isoOffset, 0, 1) . '1');

            $hours = substr($isoOffset, 1, 2);
            if (!is_numeric($hours)) {
                $hours = 0;
            }
            $minutes = substr($isoOffset, 3, 2);
            if (!is_numeric($minutes)) {
                $minutes = 0;
            }
            $calcOffset = $plusMinus * (($hours * 3600) + ($minutes * 60));
        }
        return $this->modify((-$calcOffset) . ' seconds');
    }

    /**
     * Format SugarDateTime as date, dime or datetime string in any of "db", "iso" or "user" formats
     * @param string type of the second argument : one of "date", "time", "datetime", "datetimecombo"
     * @param string output format - one of: "db", "iso" or "user"
     * @param User - optional  i.e. if type is 'user'
     * @return string formatted result
     */
    public function formatDateTime($type, $toFormat, User $user = null)
    {
        global $timedate;
        $result = '';

        switch ($toFormat) {
            case 'db':
                $result = $timedate->asDbType($this, $type);
                break;
            case 'user':
                $result = $timedate->asUserType($this, $type, $user);
                break;
            case 'iso':
            default:
                switch ($type) {
                    case 'date':
                        $result = $timedate->asIsoDate($this);
                        break;
                    case 'time':
                        $result = $timedate->asIsoTime($this);
                        break;
                    case 'datetime':
                    case 'datetimecombo':
                    default:
                        $result = $timedate->asIso($this);
                        break;
                }
                break;
        }

        return $result;
    }

    /**
     * Set Current Date to First Day of Current Month
     * @return SugarDateTime
     */
    public function setDateForFirstDayOfMonth()
    {
        $this->setDate($this->getYear(), $this->getMonth(), 1);
        return $this;
    }

    /**
     * Return a new SugarDateTime object holding the Date for the Next
     * matching Day of Week.
     * Current Date is Not Included.
     *
     * @param int $dow
     * @return SugarDateTime
     */
    public function getDateForNextDayOfWeek($dow)
    {
        $dow = $dow % 7;
        $interval = new DateInterval('P1D');
        $sdtm = clone $this;
        $sdtm->add($interval);
        while ($sdtm->getDayOfWeek() !== $dow) {
            $sdtm->add($interval);
        }
        return $sdtm;
    }

    /**
     * Return a new SugarDateTime object holding the Date for the Previous
     * matching Day of Week.
     * Current Date is Not Included.
     *
     * @param int $dow
     * @return SugarDateTime
     */
    public function getDateForPreviousDayOfWeek($dow)
    {
        $dow = $dow % 7;
        $interval = new DateInterval('P1D');
        $sdtm = clone $this;
        $sdtm->sub($interval);
        while ($sdtm->getDayOfWeek() !== $dow) {
            $sdtm->sub($interval);
        }
        return $sdtm;
    }

    /**
     * Return an array of SugarDateTime objects for all of the current month's dates
     * that match the Days of Week supplied.
     * @param array  Days Of Week (0-6)
     * @return array(SugarDateTime)
     */
    public function getMonthDatesForDaysOfWeek($dayOfWeekArray = [])
    {
        $dates = [];
        $interval = new DateInterval('P1D');
        $sdtm = clone $this;
        $sdtm->setDate($sdtm->getYear(), $sdtm->getMonth(), 1);

        $daysInMonth = $sdtm->getDaysInMonth();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (safeInArray($sdtm->getDayOfWeek(), $dayOfWeekArray)) {
                $dates[] = clone $sdtm;
            }
            $sdtm->add($interval);
        }
        return $dates;
    }

    /**
     * Return an array of SugarDateTime objects for all of the current month's dates
     * that land on a WeekEnd.
     * @return array(SugarDateTime)
     */
    public function getMonthDatesForWeekEndDays()
    {
        return $this->getMonthDatesForDaysOfWeek([self::DOW_SAT, self::DOW_SUN]);
    }

    /**
     * Return an array of SugarDateTime objects for all of the current month's dates
     * that land on a Monday through Friday.
     * @return array(SugarDateTime)
     */
    public function getMonthDatesForNonWeekEndDays()
    {
        return $this->getMonthDatesForDaysOfWeek(
            [
                self::DOW_MON,
                self::DOW_TUE,
                self::DOW_WED,
                self::DOW_THU,
                self::DOW_FRI,
            ]
        );
    }

    /**
     * Return an array of SugarDateTime objects for all of the current month's dates
     * that match the Days of Week supplied.
     * @param array  Days Of Week (0-6)
     * @return array(SugarDateTime)
     */
    public function getYearDatesForDaysOfWeek($dayOfWeekArray = [], $last = false)
    {
        $dates = [];
        $interval = new DateInterval('P1D');
        $sdtm = clone $this;
        $sdtm->setDate($sdtm->getYear(), $sdtm->getMonth(), 1);
        for ($i = 1; $i <= 45; $i++) {
            if (safeInArray($sdtm->getDayOfWeek(), $dayOfWeekArray)) {
                $dates[] = clone $sdtm;
            }
            $sdtm->add($interval);
        }
        $sdtm->setDate($sdtm->getYear(), 12, 25);
        for ($i = 1; $i <= 7; $i++) {
            if (safeInArray($sdtm->getDayOfWeek(), $dayOfWeekArray)) {
                $dates[] = clone $sdtm;
            }
            $sdtm->add($interval);
        }
        return $dates;
    }

    /**
     * Get Month of the Year
     * @return int (1-12)
     */
    public function getMonth()
    {
        return $this->format('n');
    }

    /**
     * Get Day of the Month
     * @return int (1-31)
     */
    public function getDay()
    {
        return $this->format('j');
    }

    /**
     * Get Year
     * @return int (4-digit year)
     */
    public function getYear()
    {
        return $this->format('Y');
    }

    /**
     * Get Hours
     * @return int (0-23)
     */
    public function getHour()
    {
        return $this->format('G');
    }

    /**
     * Get minutes
     * @return int (0-59)
     */
    public function getMinute()
    {
        return (int)$this->format('i');
    }

    /**
     * Get seconds
     * @return int (0-59)
     */
    public function getSecond()
    {
        return (int)$this->format('s');
    }

    /**
     * Get Number of Days in Current Month
     * @return int (28-31)
     */
    public function getDaysInMonth()
    {
        return (int)$this->format('t');
    }

    /**
     * Get Current Date's Day Of Week
     * @return int     (0=Sun, 1=Mon, ... 6=Sat)
     */
    public function getDayOfWeek()
    {
        return $this->format('w');
    }

    /**
     * Is the DayOfWeek (supplied or current Date's Day Of Week) a Week End Date (Sat/Sun) ?
     * @param $dow  (optional)
     * @return bool
     */
    public function isWeekEndDay($dow = null)
    {
        if (is_null($dow)) {
            $dow = $this->getDayOfWeek();
        } else {
            $dow = $dow % 7;
        }
        return ($dow == self::DOW_SAT || $dow == self::DOW_SUN);
    }
}
