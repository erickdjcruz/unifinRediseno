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
 * Score processing on Activities
 */
class ScoreHelper
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
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\pointsHelper
     */
    private $pointsHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parentHelper = new ParentHelper();
        $this->activityHelper = ActivityHelper::getInstance();
        $this->childActivityHelper = new ChildActivityHelper();
        $this->statusHelper = new StatusHelper();
        $this->pointsHelper = new PointsHelper();
    }

    /**
     * Retrieves the score from a activity
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function getScore(\SugarBean $activity)
    {
        return (int)$activity->customer_journey_score;
    }

    /**
     * Sets the score on a activity
     *
     * @param \SugarBean $activity
     * @param int $score
     */
    public function setScore(\SugarBean $activity, $score)
    {
        $activity->customer_journey_score = $score;
    }

    /**
     * Calculates the score of a activity
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function calculateScore(\SugarBean $activity)
    {
        $score = 0;
        $inProgressScoringFactor = 0.3;
        if ($this->parentHelper->isParent($activity)) {
            foreach ($this->childActivityHelper->getChildren($activity) as $child) {
                $score += $this->getScore($child);
            }
        } elseif ($this->statusHelper->isCancelled($activity)) {
            $score = $this->pointsHelper->getPoints($activity);
        } elseif ($this->statusHelper->isCompleted($activity)) {
            $score = $this->pointsHelper->getPoints($activity);
        } elseif ($this->statusHelper->isInProgress($activity)) {
            $score = $this->pointsHelper->getPoints($activity) * $inProgressScoringFactor;
        }

        return $score;
    }

    /**
     * Check if score has been changed
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isScoreChanged(\SugarBean $activity)
    {
        return $this->activityHelper->isFieldChanged($activity, 'customer_journey_score');
    }
}
