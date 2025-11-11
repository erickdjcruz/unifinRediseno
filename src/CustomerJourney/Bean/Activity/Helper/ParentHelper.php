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
 * parent record processing on Activities
 */
class ParentHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityHelper
     */
    private $activityHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activityHelper = ActivityHelper::getInstance();
    }

    /**
     * Checks if a activity has children
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isParent(\SugarBean $activity)
    {
        return !empty($activity->is_cj_parent_activity);
    }

    /**
     * Checks if a activity has parent
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function hasParent(\SugarBean $activity)
    {
        return !empty($activity->cj_parent_activity_id);
    }

    /**
     * Get the parent of activity
     *
     * @param \SugarBean $activity
     * @return \SugarBean
     */
    public function getParent(\SugarBean $activity)
    {
        return $this->hasParent($activity) ? \BeanFactory::retrieveBean(
            $activity->cj_parent_activity_type,
            $activity->cj_parent_activity_id
        ) : null;
    }

    /**
     * Add relationship of activity with Parent record
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parent
     */
    public function relateToParent(\SugarBean $activity, \SugarBean $parent, $linkName)
    {
        $this->activityHelper->loadRelationship($parent, $linkName);
        $parent->{$linkName}->add($activity);
    }

    /**
     * Populates a activity from the parent (Account/Contact/Lead etc)
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parent
     */
    public function populateFromParent(\SugarBean $activity, \SugarBean $parent)
    {
        $activity->parent_type = $parent->module_dir;
        $activity->parent_id = $parent->id;
        $activity->parent_name = $parent->name;
    }

    /**
     * Populates a activity from the parent activity (Task/Call/Meeting)
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parentActivity
     */
    public function populateFromParentActivity(\SugarBean $activity, \SugarBean $parent)
    {
        $activity->cj_parent_activity_type = $parent->module_dir;
        $activity->cj_parent_activity_id = $parent->id;
    }

    /**
     * Checks if a activity have changed parent
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function haveChangedParent(\SugarBean $activity)
    {
        $fetched_row_value = false;
        if (is_array($activity->fetched_row_before)) {
            $fetched_row_value = $activity->fetched_row_before['parent_id'];
        }

        return $fetched_row_value !== $activity->parent_id;
    }
}
