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
 * This class here to have functions for the
 * points processing on Activities
 */
class PointsHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\parentHelper
     */
    private $parentHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityHelper
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
     * Constructor
     */
    public function __construct()
    {
        $this->parentHelper = new ParentHelper();
        $this->activityHelper = ActivityHelper::getInstance();
        $this->childActivityHelper = new ChildActivityHelper();
        $this->statusHelper = new StatusHelper();
    }

    /**
     * Retrieves the points from a activity
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function getPoints(\SugarBean $activity)
    {
        return (int)$activity->customer_journey_points;
    }

    /**
     * Retrieves the points from a activity
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function calculatePoints(\SugarBean $activity)
    {
        $template = null;
        $points = 0;

        if ($this->parentHelper->isParent($activity)) {
            foreach ($this->childActivityHelper->getChildren($activity) as $child) {
                $points += $this->getPoints($child);
            }
        }

        if (0 === $points) {
            if (!empty($activity->dri_workflow_template_id)) {
                $template = \DRI_Workflow_Template::getById($activity->dri_workflow_template_id);
            } elseif (!empty($activity->dri_subworkflow_id)) {
                $template = \DRI_Workflow_Template::getBeanByActivityId($activity);
            }
            if (!empty($template->id)) {
                if ($template->not_applicable_action === 'custom' &&
                    ($this->statusHelper->isNotApplicable($activity) ||
                        $this->statusHelper->isCancelled($activity))) {
                    $points = 0;
                } else {
                    $points = (int)$activity->customer_journey_points;
                }
            } else {
                $points = (int)$activity->customer_journey_points;
            }
        }

        return $points;
    }

    /**
     * Sets the score on a activity
     *
     * @param \SugarBean $activity
     * @param int $points
     */
    public function setPoints(\SugarBean $activity, $points)
    {
        $activity->customer_journey_points = $points;
    }

    /**
     * Checks if a activity have changed points
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function haveChangedPoints(\SugarBean $activity)
    {
        $fetched_row_value = false;
        if (is_array($activity->fetched_row_before)) {
            $fetched_row_value = $activity->fetched_row_before['customer_journey_points'];
        }

        return (int)$fetched_row_value !== $activity->customer_journey_points;
    }

    /**
     * Check if points has been changed
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isPointsChanged(\SugarBean $activity)
    {
        return $this->activityHelper->isFieldChanged($activity, 'customer_journey_points');
    }
}
