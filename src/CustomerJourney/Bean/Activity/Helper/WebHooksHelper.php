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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;

/**
 * This class here to have functions for the
 * WebHooks processing on Activities
 */
class WebHooksHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\parentHelper
     */
    private $parentHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\ActivityHelper
     */
    private $activityHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\childActivityHelper
     */
    private $childActivityHelper;

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
        $this->parentHelper = new ParentHelper();
        $this->activityHelper = ActivityHelper::getInstance();
        $this->childActivityHelper = new ChildActivityHelper();
        $this->statusHelper = new StatusHelper();
        $this->stageHelper = new StageHelper();
    }

    /**
     * Gets called when a activity gets completed (after save)
     *
     * @param \DRI_Workflow $journey
     * @param \DRI_SubWorkflow $stage
     * @param \SugarBean $activity
     */
    public function afterCompleted(\DRI_Workflow $journey, \DRI_SubWorkflow $stage, \SugarBean $activity)
    {
        if ($this->parentHelper->hasParent($activity)) {
            $next = $this->childActivityHelper->getNextChildActivity($activity);
        } else {
            $next = $journey->getNextActivity($stage, $activity);
        }

        if ($next) {
            $handler = ActivityHandlerFactory::factory($next->module_dir);
            $handler->previousActivityCompleted($next, $activity);
        }
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_AFTER_COMPLETED);

        if ($this->statusHelper->isCompleted($activity) &&
            !$this->statusHelper->isNotApplicable($activity) &&
            $this->stageHelper->isStageActivity($activity) &&
            $this->activityHelper->hasActivityTemplate($activity)) {
            $journey->setMomentumStartDateOfTargetedActivities($activity);
            $journey->setDatesAndAssigneeOfDependentActivities($activity);
        }

        if ($activity->setAssignmentSummary === true && $activity->assignmentChangeOnCompleted === true) {
            $journey->sendAssignmentNotifications();
        }
    }

    /**
     * Gets called when a activity gets completed (before save)
     *
     * @param \SugarBean $activity
     */
    public function beforeCompleted(\SugarBean $activity)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_BEFORE_COMPLETED);
    }

    /**
     * Gets called when a activity gets InProgress (after save)
     *
     * @param \SugarBean $activity
     */
    public function afterInProgress(\SugarBean $activity)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_AFTER_IN_PROGRESS);
    }

    /**
     * Gets called when a activity gets InProgress (before save)
     *
     * @param \SugarBean $activity
     */
    public function beforeInProgress(\SugarBean $activity)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_BEFORE_IN_PROGRESS);
    }

    /**
     * Gets called when a activity gets NotApplicable (after save)
     *
     * @param \SugarBean $activity
     */
    public function afterNotApplicable(\SugarBean $activity)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_AFTER_NOT_APPLICABLE);
    }

    /**
     * Gets called when a activity gets InProgress (before save)
     *
     * @param \SugarBean $activity
     */
    public function beforeNotApplicable(\SugarBean $activity)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_BEFORE_NOT_APPLICABLE);
    }

    /**
     * Gets called when a activity gets deleted (after save)
     *
     * @param \SugarBean $activity
     */
    public function afterDelete(\SugarBean $activity)
    {
        // no op
    }

    /**
     * Gets called when a activity gets deleted (before save)
     *
     * @param \SugarBean $activity
     */
    public function beforeDelete(\SugarBean $activity)
    {
        // no op
    }

    /**
     * Will be called after the activity has been created
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parent
     */
    public function afterCreate(\SugarBean $activity, \SugarBean $parent)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_AFTER_CREATE);
    }

    /**
     * Will be called before the activity has been created
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parent
     */
    public function beforeCreate(\SugarBean $activity, \SugarBean $parent)
    {
        $this->sendWebHooks($activity, \CJ_WebHook::TRIGGER_EVENT_BEFORE_CREATE);
    }

    /**
     * Call the webhook registerd for the template level
     * on the Hooks
     *
     * @param \SugarBean $activity
     * @param string $trigger_event
     */
    public function sendWebHooks(\SugarBean $activity, $trigger_event)
    {
        if (!$this->activityHelper->hasActivityTemplate($activity)) {
            return;
        }

        $template = $this->activityHelper->getActivityTemplate($activity);
        $stage = $this->stageHelper->getStage($activity);
        try {
            $parent = $stage->getParent();
        } catch (CJException\ParentNotFoundException $e) {
            $parent = null;
        } catch (CJException\NotFoundException $e) {
            return;
        }
        $journey = $stage->getJourney();

        $template->sendWebHooks($trigger_event, [
            'parent_module' => (is_null($parent)) ? $parent : $parent->module_dir,
            'parent' => (is_null($parent)) ? $parent : $parent->toArray(true),
            'journey' => $journey->toArray(true),
            'stage' => $stage->toArray(true),
            'activity' => $activity->toArray(true),
        ]);
    }
}
