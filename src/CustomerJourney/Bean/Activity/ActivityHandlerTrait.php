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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper as ActivitiesHelpers;

/**
 * Trait for AbstractActivityHandler class and here we
 * defined/declare the object of all the activities helpers
 * that will be used in logics.
 * Helpers are defined in Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper
 * So in future, if any helper added/removed then first update it
 * and then use that object in the handlers.
 */
trait ActivityHandlerTrait
{
    public $webooksHelper;
    public $statusHelper;
    public $scoreHelper;
    public $stageHelper;
    public $progressHelper;
    public $pointsHelper;
    public $blockedByHelper;
    public $childActivityHelper;
    public $momentumHelper;
    public $parentHelper;
    public $activityDatesHelper;
    public $abstractActivityHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->webooksHelper = new ActivitiesHelpers\WebHooksHelper();
        $this->statusHelper = new ActivitiesHelpers\StatusHelper();
        $this->scoreHelper = new ActivitiesHelpers\ScoreHelper();
        $this->stageHelper = new ActivitiesHelpers\StageHelper();
        $this->progressHelper = new ActivitiesHelpers\ProgressHelper();
        $this->pointsHelper = new ActivitiesHelpers\PointsHelper();
        $this->blockedByHelper = new ActivitiesHelpers\BlockedByHelper();
        $this->childActivityHelper = new ActivitiesHelpers\ChildActivityHelper();
        $this->momentumHelper = new ActivitiesHelpers\MomentumHelper();
        $this->parentHelper = new ActivitiesHelpers\ParentHelper();
        $this->activityDatesHelper = new ActivitiesHelpers\ActivityDatesHelper();
    }
}
