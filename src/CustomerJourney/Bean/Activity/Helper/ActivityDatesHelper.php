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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper;

/**
 * This class here to provide functions for the
 * date processing on Activities
 */
class ActivityDatesHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\statusHelper
     */
    private $statusHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\stageHelper
     */
    private $stageHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->statusHelper = new StatusHelper();
        $this->stageHelper = new StageHelper();
    }

    /**
     * Set Activity Start and End Dates (Meetings and Calls only)
     *
     * @param \SugarBean $activity
     * @param string $status
     */
    public function setActivityStartAndEndDates(\SugarBean $activity, $status)
    {
        if ($activity->getModuleName() !== 'Tasks') {
            return;
        }
        if ($status === $this->statusHelper->getInProgressStatus($activity)) {
            $activity->cj_activity_start_date = $GLOBALS['timedate']->nowDb();
        }
        if ($status === $this->statusHelper->getCompletedStatus($activity)) {
            $activity->cj_activity_completion_date = $GLOBALS['timedate']->nowDb();
        }
    }

    /**
     * Calculate the difference between start date and due date in days
     *
     * @param \SugarBean $activity
     * @param \DateTime $dueDate
     * @return Array with days and hours
     */
    public function calculateTheDiffBetweenStartDateAndDueDateInDays(\SugarBean $activity, \DateTime $dueDate)
    {
        $timeDate = \TimeDate::getInstance();
        if (empty($activity->date_start)) {
            return [];
        }

        $startDate = $timeDate->fromString($activity->date_start);

        //calculate the difference between start date and due date
        $interval = $dueDate->diff($startDate);
        $diff_days = 0;

        if ($interval->invert) { // if difference is positive
            $diff_days = $interval->format('%a');
            if ($interval->h < 0) {
                $diff_days++;
            }
        }

        return [$diff_days, $interval->h];
    }

    /**
     * This function will return the Due Date which is calculated by adding the days in Start Date
     * and also the hours which are remaining
     *
     * @param \DRI_Workflow_Task_Template $template
     * @param \SugarBean $bean
     * @param \DateTime $date
     * @return \DateTime Due Date
     */
    public function setDueDateInPercentageTimeFrame(\DRI_Workflow_Task_Template $template, \SugarBean $bean, \DateTime $date)
    {
        $timeDate = \TimeDate::getInstance();
        $dueDate = clone $date;

        // set correct timezone
        $timeDate->tzUser($dueDate);
        $startDate = $timeDate->fromString($bean->date_start);
        $dueDateInPercentageTimeFrame = $startDate;

        $diffDaysHours = $this->calculateTheDiffBetweenStartDateAndDueDateInDays($bean, $dueDate);

        // add the days
        if (is_array($diffDaysHours) &&
            safeCount($diffDaysHours) > 1 &&
            $diffDaysHours[0] > 0) {
            //Difference between start date and due date is positive
            //Calculate the percentage only if value is between -100 to 100
            $startDueDateDaysDiff = $diffDaysHours[0];
            if ($template->task_due_days <= 100 && $template->task_due_days >= -100) {
                $startDueDateDaysDiff = ($template->task_due_days * $diffDaysHours[0]) / 100;
                $startDueDateDaysDiff = (int)$startDueDateDaysDiff;
            }

            if ($template->task_due_days > 0) {
                $dueDateInPercentageTimeFrame->modify(sprintf('+ %s days', $startDueDateDaysDiff));
            } elseif ($template->task_due_days < 0) {
                $dueDateInPercentageTimeFrame->modify(sprintf('- %s days', $diffDaysHours[0]));
            }
        }

        if ($bean->module_dir === 'Meetings') {
            // If days are in positive then we need to add the the remaining hours otherwise
            // subtract them
            $startDueDateHoursDiff = $diffDaysHours[1];
            if ($diffDaysHours[0] > 0) {
                $dueDateInPercentageTimeFrame->modify(sprintf('+ %s hours', $startDueDateHoursDiff));
            } elseif ($diffDaysHours[0] < 0) {
                $dueDateInPercentageTimeFrame->modify(sprintf('- %s hours', $startDueDateHoursDiff));
            }
        }

        return $dueDateInPercentageTimeFrame;
    }

    /**
     * Get the due date from the Parent field
     *
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Task_Template $template
     * @return \DateTime|null
     * @throws \NotFoundException
     * @throws \ParentNotFoundException
     */
    public function getDueDateFromParentField(\SugarBean $activity, \DRI_Workflow_Task_Template $template)
    {
        $stage = $this->stageHelper->getStage($activity);
        $parent = $stage->getParent($template->due_date_module);
        $def = $parent->getFieldDefinition($template->due_date_field);
        $value = $parent->{$template->due_date_field};

        return $this->formatGivenDate($def['type'], $value);
    }

    /**
     * Get the start date from the Parent field
     *
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Task_Template $template
     * @return \DateTime|null
     * @throws \NotFoundException
     * @throws \ParentNotFoundException
     */
    public function getStartDateFromParentField(\SugarBean $activity, \DRI_Workflow_Task_Template $template)
    {
        $stage = $this->stageHelper->getStage($activity);
        $parent = $stage->getParent($template->start_date_module);
        $def = $parent->getFieldDefinition($template->start_date_field);
        $value = $parent->{$template->start_date_field};

        return $this->formatGivenDate($def['type'], $value);
    }

    /**
     * Format the dates according to DB format
     *
     * @param string $type
     * @param string $value
     *
     * @return \DateTime|null
     */
    public function formatGivenDate($type, $value)
    {
        if (!empty($value)) {
            $timeDate = \TimeDate::getInstance();
            if (in_array($type, ['datetime', 'datetimecombo'])) {
                $date = $timeDate->fromUser($value);

                if (!$date) {
                    $date = $timeDate->fromDb($value);
                }
            } else {
                $date = $timeDate->fromUserDate($value);

                if (!$date) {
                    $date = $timeDate->fromDbDate($value);
                }
            }

            return $date;
        }
    }
}
