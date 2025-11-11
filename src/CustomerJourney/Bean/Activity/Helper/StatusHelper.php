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
 * status processing on Activities
 */
class StatusHelper
{
    /**
     * @var CONST
     */
    public const APPOINMENT_STATUS_PLANNED = 'Planned';

    /**
     * @var CONST
     */
    public const APPOINMENT_STATUS_STARTED = 'In Progress';

    /**
     * @var CONST
     */
    public const APPOINMENT_STATUS_HELD = 'Held';

    /**
     * @var CONST
     */
    public const APPOINMENT_STATUS_NOT_HELD = 'Not Held';

    /**
     * @var CONST
     */
    public const APPOINMENT_STATUS_DEFERRED = 'Not Applicable';

    /**
     * @var CONST
     */
    public const TASK_STATUS_NOT_STARTED = 'Not Started';

    /**
     * @var CONST
     */
    public const TASK_STATUS_IN_PROGRESS = 'In Progress';

    /**
     * @var CONST
     */
    public const TASK_STATUS_COMPLETED = 'Completed';

    /**
     * @var CONST
     */
    public const TASK_STATUS_NOT_APPLICABLE = 'Not Applicable';

    /**
     * @var CONST
     */
    public const TASK_STATUS_DEFERRED = 'Not Applicable';

    /**
     * @var array
     * Mapping for the status key/value for Tasks, Meetings and Calls
     */
    private static $statusMapper = [
        'Calls' => [
            'statusMapping' => [
                'notStart' => self::APPOINMENT_STATUS_PLANNED,
                'inProgress' => self::APPOINMENT_STATUS_STARTED,
                'complete' => self::APPOINMENT_STATUS_HELD,
                'notApplicable' => self::APPOINMENT_STATUS_NOT_HELD,
                'cancelled' => self::APPOINMENT_STATUS_DEFERRED,
            ],
            'statusArray' => [
                self::APPOINMENT_STATUS_STARTED,
                self::APPOINMENT_STATUS_PLANNED,
                self::APPOINMENT_STATUS_HELD,
                self::APPOINMENT_STATUS_NOT_HELD,
                self::APPOINMENT_STATUS_DEFERRED,
            ],
            'completed_status_list' => 'cj_calls_completed_status_list',
        ],
        'Meetings' => [
            'statusMapping' => [
                'notStart' => self::APPOINMENT_STATUS_PLANNED,
                'inProgress' => self::APPOINMENT_STATUS_PLANNED,
                'complete' => self::APPOINMENT_STATUS_HELD,
                'notApplicable' => self::APPOINMENT_STATUS_NOT_HELD,
                'cancelled' => self::APPOINMENT_STATUS_DEFERRED,
            ],
            'statusArray' => [
                self::APPOINMENT_STATUS_PLANNED,
                self::APPOINMENT_STATUS_HELD,
                self::APPOINMENT_STATUS_NOT_HELD,
                self::APPOINMENT_STATUS_DEFERRED,
            ],
            'completed_status_list' => 'cj_meetings_completed_status_list',
        ],
        'Tasks' => [
            'statusMapping' => [
                'notStart' => self::TASK_STATUS_NOT_STARTED,
                'inProgress' => self::TASK_STATUS_IN_PROGRESS,
                'complete' => self::TASK_STATUS_COMPLETED,
                'notApplicable' => self::TASK_STATUS_NOT_APPLICABLE,
                'cancelled' => self::TASK_STATUS_DEFERRED,
            ],
            'statusArray' => [
                self::TASK_STATUS_NOT_STARTED,
                self::TASK_STATUS_IN_PROGRESS,
                self::TASK_STATUS_COMPLETED,
                self::TASK_STATUS_NOT_APPLICABLE,
                self::TASK_STATUS_DEFERRED,
            ],
            'completed_status_list' => 'cj_tasks_completed_status_list',
        ],
    ];

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityHelper
     */
    private $activityHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\childActivityHelper
     */
    private $childActivityHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activityHelper = ActivityHelper::getInstance();
        $this->childActivityHelper = new ChildActivityHelper();
    }

    /**
     * Return the Not Started stautus key of Activity
     *
     * @param \SugarBean $activity
     * @param string $module_name
     * @return string
     */
    public function getNotStartedStatus(\SugarBean $activity, $module_name = '')
    {
        if (empty($module_name)) {
            $module_name = $activity->getModuleName();
        }

        return static::$statusMapper[$module_name]['statusMapping']['notStart'] ?? '';
    }

    /**
     * Return the InProgress stautus key of Activity
     *
     * @param \SugarBean $activity
     * @param string $module_name
     * @return string
     */
    public function getInProgressStatus(\SugarBean $activity, $module_name = '')
    {
        if (empty($module_name)) {
            $module_name = $activity->getModuleName();
        }

        return static::$statusMapper[$module_name]['statusMapping']['inProgress'] ?? '';
    }

    /**
     * Return the Completed stautus key of Activity
     *
     * @param \SugarBean $activity
     * @param string $module_name
     * @return string
     */
    public function getCompletedStatus(\SugarBean $activity, $module_name = '')
    {
        if (empty($module_name)) {
            $module_name = $activity->getModuleName();
        }

        return static::$statusMapper[$module_name]['statusMapping']['complete'] ?? '';
    }

    /**
     * Return the Not Applicable stautus key of Activity
     *
     * @param \SugarBean $activity
     * @param string $module_name
     * @return string
     */
    public function getNotApplicableStatus(\SugarBean $activity, $module_name = '')
    {
        if (empty($module_name)) {
            $module_name = $activity->getModuleName();
        }

        return static::$statusMapper[$module_name]['statusMapping']['notApplicable'] ?? '';
    }

    /**
     * Return the Cancelled status key of Activity
     *
     * @param \SugarBean $activity
     * @param string $module_name
     * @return string
     */
    public function getCancelledStatus(\SugarBean $activity, $module_name = '')
    {
        if (empty($module_name)) {
            $module_name = $activity->getModuleName();
        }

        return static::$statusMapper[$module_name]['statusMapping']['cancelled'] ?? '';
    }

    /**
     * Checks if a activity is started
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isInProgress(\SugarBean $activity)
    {
        if (in_array($activity->getModuleName(), ['Meetings'])) {
            return false;
        }

        return $activity->status === static::$statusMapper[$activity->getModuleName()]['statusMapping']['inProgress'];
    }

    /**
     * Checks if a activity is not started
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isNotStarted(\SugarBean $activity)
    {
        return $activity->status === static::$statusMapper[$activity->getModuleName()]['statusMapping']['notStart'];
    }

    /**
     * Checks if a activity is Not applicable
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isNotApplicable(\SugarBean $activity)
    {
        return $activity->status === static::$statusMapper[$activity->getModuleName()]['statusMapping']['notApplicable'];
    }

    /**
     * Checks if a activity is completed
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isCompleted(\SugarBean $activity)
    {
        return isset($GLOBALS['app_list_strings'][static::$statusMapper[$activity->getModuleName()]['completed_status_list']][$activity->status]);
    }

    /**
     * Checks if a activity is Cancelled
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isCancelled(\SugarBean $activity)
    {
        return $activity->status === static::$statusMapper[$activity->getModuleName()]['statusMapping']['cancelled'] ?? '';
    }

    /**
     * Calculate the status
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function calculateStatus(\SugarBean $activity)
    {
        $children = $this->childActivityHelper->getChildren($activity);
        $count = safeCount($children);
        $notStarted = 0;
        $completed = 0;
        $notApplicable = 0;
        $cancelled = 0;

        foreach ($children as $child) {
            if ($this->isNotStarted($child)) {
                $notStarted++;
            } elseif ($this->isNotApplicable($child)) {
                $notApplicable++;
            } elseif ($this->isCompleted($child)) {
                $completed++;
            } elseif ($this->isCancelled($child)) {
                $cancelled++;
            }
        }

        if ($notStarted === $count) {
            $this->setStatus($activity, $this->getNotStartedStatus($activity));
        } elseif ($notApplicable === $count) {
            $this->setStatus($activity, $this->getNotApplicableStatus($activity));
        } elseif (($completed + $notApplicable) === $count) {
            $this->setStatus($activity, $this->getCompletedStatus($activity));
        } elseif ($cancelled + $completed === $count) {
            $this->setStatus($activity, $this->getCancelledStatus($activity));
        } else {
            $this->setStatus($activity, $this->getInProgressStatus($activity));
        }

        return $this->isStatusChanged($activity);
    }

    /**
     * Sets the status on a activity
     *
     * @param \SugarBean $activity
     * @param string $status
     */
    public function setStatus(\SugarBean $activity, $status)
    {
        $activity->status = $status;
    }

    /**
     * Check if status is changed
     * @param \SugarBean $activity
     * @return bool
     */
    public function isStatusChanged(\SugarBean $activity)
    {
        return $this->activityHelper->isFieldChanged($activity, 'status');
    }

    /**
     * Check if status is valid or not
     *
     * @param string $module_name
     * @param string $status
     * @return bool
     */
    public function isValidStatus($module_name, $status)
    {
        return in_array($status, static::$statusMapper[$module_name]['statusArray']);
    }

    /**
     * Checks if a activity have changed status
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function haveChangedStatus(\SugarBean $activity)
    {
        $fetched_row_value = false;
        if (is_array($activity->fetched_row_before) && !empty($activity->fetched_row_before['status'])) {
            $fetched_row_value = $activity->fetched_row_before['status'];
        }

        return $fetched_row_value !== $activity->status;
    }
}
