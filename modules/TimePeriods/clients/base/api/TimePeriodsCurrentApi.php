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

class TimePeriodsCurrentApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'currentTimeperiod' => [
                'reqType' => 'GET',
                'path' => ['TimePeriods', 'current'],
                'pathVars' => ['module', ''],
                'method' => 'getCurrentTimePeriod',
                'jsonParams' => [],
                'shortHelp' => 'Return the Current Timeperiod',
                'longHelp' => 'modules/TimePeriods/clients/base/api/help/TimePeriodsCurrentApi.html',
            ],
            'getTimePeriodByDate' => [
                'reqType' => 'GET',
                'path' => ['TimePeriods', '?'],
                'pathVars' => ['module', 'date'],
                'method' => 'getTimePeriodByDate',
                'jsonParams' => [],
                'shortHelp' => 'Return a Timeperiod by a given date',
                'longHelp' => 'modules/TimePeriods/clients/base/api/help/TimePeriodsGetByDateApi.html',
            ],
        ];
    }

    public function getCurrentTimePeriod(ServiceBase $api, array $args)
    {
        $tp = TimePeriod::getCurrentTimePeriod();

        if (is_null($tp)) {
            // return a 404
            throw new SugarApiExceptionNotFound();
        }

        return $tp->toArray();
    }

    public function getTimePeriodByDate(ServiceBase $api, array $args)
    {
        if (!isset($args['date']) || $args['date'] == 'undefined') {
            // return a 404
            throw new SugarApiExceptionNotFound();
        }

        $tp = TimePeriod::retrieveFromDate($args['date']);

        return ($tp) ? $tp->toArray() : $tp;
    }
}
