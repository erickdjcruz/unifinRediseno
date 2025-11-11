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

use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\ActivityHooksHelper;

/**
 * This class contains logic hooks related to the
 * Smart Guide plugin for the activity modules
 */
class ActivityHooks
{
    /**
     * @var \Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\ActivityHooksHelper|mixed
     */
    public $hooksHelper;

    public function __construct()
    {
        $this->hooksHelper = new ActivityHooksHelper();
    }

    /**
     * All before_save logic hooks is inside this function.
     *
     * @param object $activity
     * @param string $event
     * @param array $arguments
     */
    public function beforeSave($activity, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }
        $this->hooksHelper->saveFetchedRow($activity);
        $this->hooksHelper->validate($activity);
        $this->hooksHelper->reorder($activity);
        $this->hooksHelper->calculate($activity);
        $this->hooksHelper->calculateMomentum($activity);
        $this->hooksHelper->beforeStatusChange($activity);
        $this->hooksHelper->setActualSortOrder($activity);
        $this->hooksHelper->checkRelatedSugarAction($activity, $event, $arguments);

        if ($activity->getModuleName() === 'Tasks') {
            $this->hooksHelper->setCJDaysToComplete($activity);
        }
    }

    /**
     * All after_save logic hooks is inside this function.
     *
     * @param object $activity
     * @param string $event
     * @param array $arguments
     */
    public function afterSave($activity, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }
        if ($this->isStatusProgressUpdated($arguments)) {
            $this->hooksHelper->resaveIfChanged($activity);
            $this->hooksHelper->startNextJourneyIfCompleted($activity, $event, $arguments);
        }
    }

    /**
     * check if the data is changed or not
     *
     * @param array $arguments
     */
    public function isStatusProgressUpdated(array $arguments)
    {
        return (isset($arguments['dataChanges']['status']) &&
                isset($arguments['dataChanges']['customer_journey_progress']) &&
                isset($arguments['dataChanges']['customer_journey_score'])) || isset($arguments['dataChanges']['customer_journey_points']);
    }

    /**
     * All before_delete logic hooks is inside this function.
     *
     * @param object $activity
     * @param string $event
     * @param array $arguments
     */
    public function beforeDelete($activity, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }
        $this->hooksHelper->beforeDelete($activity);
        $this->hooksHelper->removeChildren($activity);
    }

    /**
     * All after_delete logic hooks is inside this function.
     *
     * @param object $activity
     * @param string $event
     * @param array $arguments
     */
    public function afterDelete($activity, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }
        $this->hooksHelper->afterDelete($activity);
        $this->hooksHelper->resave($activity);
    }
}
