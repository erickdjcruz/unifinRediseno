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

abstract class SugarForecasting_AbstractForecast extends SugarForecasting_AbstractForecastArgs implements SugarForecasting_ForecastProcessInterface
{
    /**
     * Where we store the data we want to use
     *
     * @var array
     */
    protected $dataArray = [];

    /**
     * Return the data array
     *
     * @return array
     */
    public function getDataArray()
    {
        return $this->dataArray;
    }

    /**
     * Get the months for the current time period
     *
     * @param $timeperiod_id
     * @return array
     */
    protected function getTimePeriodMonths($timeperiod_id)
    {
        /* @var $timeperiod TimePeriod */
        $timeperiod = BeanFactory::getBean('TimePeriods', $timeperiod_id);

        $months = [];

        $start = strtotime($timeperiod->start_date);
        $end = strtotime($timeperiod->end_date);
        while ($start < $end) {
            $months[] = date('F Y', $start);
            $start = strtotime('+1 month', $start);
        }

        return $months;
    }

    /**
     * Utility method to convert a date time string into an ISO data time string for Sidecar usage.
     *
     * @param string $dt_string Date Time value to from the db to convert into ISO format for Sidecar to consume
     * @return string               The ISO version of the string
     */
    protected function convertDateTimeToISO($dt_string)
    {
        $timedate = TimeDate::getInstance();
        if ($timedate->check_matching_format($dt_string, TimeDate::DB_DATETIME_FORMAT) === false) {
            $dt_string = $timedate->to_db($dt_string);
        }
        global $current_user;
        return $timedate->asIso($timedate->fromDb($dt_string), $current_user);
    }
}
