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


class SugarWidgetFieldparent_type extends SugarWidgetFieldEnum
{
    public function __construct(&$layout_manager)
    {
        parent::__construct($layout_manager);
        $this->reporter = $this->layout_manager->getAttribute('reporter');
    }

    /**
     * Create a partial SQL query to do an equal query in a WHERE clause.
     *
     * @param $layout_def
     * @return string
     */
    public function queryFilterEquals($layout_def)
    {
        // 'equals' and 'is' are not always the same, but they are the same for parent_type
        // so we can re-use queryFilteris
        return $this->queryFilteris($layout_def);
    }

    /**
     * Create a partial SQL query to do a not equal query in a WHERE clause.
     *
     * @param $layout_def
     * @return string
     */
    public function queryFilterNot_Equals(&$layout_def)
    {
        return $this->queryFilteris_not($layout_def);
    }

    public function displayListPlain($layout_def)
    {
        $value = $this->_get_list_value($layout_def);
        if (isset($layout_def['widget_type']) && $layout_def['widget_type'] == 'checkbox') {
            if ($value != '' && ($value == 'on' || intval($value) == 1 || $value == 'yes')) {
                return "<input name='checkbox_display' class='checkbox' type='checkbox' disabled='true' checked>";
            }
            return "<input name='checkbox_display' class='checkbox' type='checkbox' disabled='true'>";
        }
        return $value;
    }
}
