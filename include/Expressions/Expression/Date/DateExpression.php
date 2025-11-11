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

abstract class DateExpression extends AbstractExpression
{
    /**
     * All parameters have to be a string.
     */
    public static function getParameterTypes()
    {
        return AbstractExpression::$DATE_TYPE;
    }

    /**
     * @static
     * @param string $date String to be parsed
     *
     * @return DateTime|boolean the DateTime object representing the string passed in
     *                          or false if the string is empty
     * @throws Exception        if the string could not be converted to a valid date
     */
    public static function parse($date)
    {
        if ($date instanceof DateTime) {
            return $date;
        }

        if (empty($date)) {
            return false;
        }

        //String dates must be in User format.
        if (is_string($date)) {
            $timedate = TimeDate::getInstance();
            if (static::hastime($date)) {
                // have time
                $resdate = $timedate->fromUser($date);
            } else {
                // just date, no time
                $resdate = $timedate->fromUserDate($date);
            }
            if (!$resdate) {
                throw new Exception("attempt to convert invalid value to date: $date");
            }
            return $resdate;
        }
        throw new Exception('attempt to convert invalid non-string value to date');
    }

    /**
     * Do we have a time param with the date param
     *
     * @param $date
     * @return bool
     */
    public static function hasTime($date)
    {
        $timedate = TimeDate::getInstance();
        $split = $timedate->split_date_time($date);

        return !empty($split[1]);
    }

    /**
     * Rounds a DateTime object to the nearest 15 minute interval
     *
     * @static
     * @param DateTime $date the DateTime object to perform rounding on
     * @param string $direction the direction in which to round. Options are 'down' or 'up', default is 'up'
     * @return DateTime $date rounded to the nearest 15 minute interval in the given direction
     */
    public static function roundTime($date, $direction = 'up')
    {
        if (!($date instanceof DateTime)) {
            return false;
        }

        $min = $date->format('i');
        $remainder = $min % 15;
        if ($remainder != 0) {
            if ($direction === 'down') {
                $date->modify("-$remainder minutes");
            } else {
                $offset = 15 - $remainder;
                $date->modify("+$offset minutes");
            }
        }

        return $date;
    }
}
