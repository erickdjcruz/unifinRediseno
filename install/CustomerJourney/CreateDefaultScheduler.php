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

$schedulerArgs = [
    'name' => 'Update Momentum of In-Progress Smart Guides',
    'job' => 'function::updateMomentumCJ',
    'date_time_start' => create_current_date_time(),
    'job_interval' => '0::0::*::*::*',
    'status' => 'Active',
    'catch_up' => '1',
];

if (createSchedulerJob($schedulerArgs)) {
    $GLOBALS['log']->info('Momentum Calculation Scheduler is Created...');
} else {
    $GLOBALS['log']->info('Momentum Calculation Scheduler creation failed. Please check logs...');
}

/**
 * Creates scheduler if not exists
 *
 * @param array $args params for scheduler
 * @return boolean
 */
function createSchedulerJob($args = [])
{
    if (empty($args) || empty($args['job']) || empty($args['job_interval'])) {
        return false;
    }

    $bean = BeanFactory::newBean('Schedulers');
    $schedulerID = isSchedulerExist($bean, ['job' => $args['job'], 'deleted' => '0']);

    if (!empty($schedulerID)) {
        return true;
    }

    foreach ($args as $field => $val) {
        if (!empty($field)) {
            $bean->$field = $val;
        }
    }

    if ($bean->save()) {
        return true;
    }

    $GLOBALS['log']->fatal('Unable to create the scheduler ' . $args['name']);
    return false;
}

/**
 * Check if scheduler exists or not
 *
 * @param SugarBean $bean
 * @param array $args associative array of fields and their values to use in where clause with and operator
 * @return string
 */
function isSchedulerExist($bean, $args = [])
{
    try {
        if (empty($args)) {
            return false;
        }

        if (empty($bean)) {
            $bean = BeanFactory::newBean('Schedulers');
        }

        $sugarQuery = new SugarQuery();
        $sugarQuery->select(['id']);
        $sugarQuery->from($bean, ['team_security' => false]);

        foreach ($args as $fieldName => $fieldValue) {
            $sugarQuery->where()->equals($fieldName, $fieldValue);
        }

        return $sugarQuery->getOne();
    } catch (Exception $e) {
        $GLOBALS['log']->fatal('Exception occurred in function::' . __function__);
        $GLOBALS['log']->fatal(print_r($e->getMessage(), true));
        $GLOBALS['log']->fatal(print_r($e->getTraceAsString(), true));
    }
}
