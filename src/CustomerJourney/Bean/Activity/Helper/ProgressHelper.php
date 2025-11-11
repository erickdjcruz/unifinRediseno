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
 * progress processing on Activities
 */
class ProgressHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityHelper
     */
    private $activityHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\scoreHelper
     */
    private $scoreHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\pointsHelper
     */
    private $pointsHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activityHelper = ActivityHelper::getInstance();
        $this->scoreHelper = new ScoreHelper();
        $this->pointsHelper = new PointsHelper();
    }

    /**
     * Sets the progress on a activity
     *
     * @param \SugarBean $activity
     * @param int $progress
     */
    public function setProgress(\SugarBean $activity, $progress)
    {
        $activity->customer_journey_progress = $progress;
    }

    /**
     * Retrieves the progress from a activity
     *
     * @param \SugarBean $activity
     * @return float
     */
    public function getProgress(\SugarBean $activity)
    {
        return $activity->customer_journey_progress;
    }

    /**
     * Calculates the progress of a activity
     *
     * @param \SugarBean $activity
     * @return float
     */
    public function calculateProgress(\SugarBean $activity)
    {
        $points = $this->pointsHelper->getPoints($activity);
        $score = $this->scoreHelper->getScore($activity);
        return $points > 0 ? round($score / $points, 2) : 0;
    }

    /**
     * Check if progress has been changed
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isProgressChanged(\SugarBean $activity)
    {
        return $this->activityHelper->isFieldChanged($activity, 'customer_journey_progress');
    }
}
