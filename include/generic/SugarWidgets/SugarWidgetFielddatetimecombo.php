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


class SugarWidgetFieldDateTimecombo extends SugarWidgetFieldDateTime
{
    public $reporter;
    public $assigned_user = null;

    public function __construct(&$layout_manager)
    {
        parent::__construct($layout_manager);
        $this->reporter = $this->layout_manager->getAttribute('reporter');
    }
    //TODO:now for date time field , we just search from date start to date end. The time is from 00:00:00 to 23:59:59
    //If there is requirement, we can modify report.js::addFilterInputDatetimesBetween and this function
    public function queryFilterBetween_Datetimes(&$layout_def)
    {
        if ($this->getAssignedUser()) {
            // report's assigned user' timezone is already taken into account in the _get_column_select method
            // via add_tz_offset which adds (+/- INTERVAL X MINUTE) to the first part of the condition
            $begin = $layout_def['input_name0'];
            $end = $layout_def['input_name2'];
        } else {
            $begin = $layout_def['input_name0'];
            $end = $layout_def['input_name1'];
        }

        return '(' . $this->_get_column_select($layout_def) . '>=' . $this->reporter->db->convert($this->reporter->db->quoted($begin), 'datetime') .
            " AND\n " . $this->_get_column_select($layout_def) . '<=' . $this->reporter->db->convert($this->reporter->db->quoted($end), 'datetime') .
            ")\n";
    }

    /**
     * Get datetime value for sidecar field
     *
     * @param array $layoutDef
     *
     * @return mixed
     */
    public function getFieldControllerData(array $layoutDef)
    {
        $value = parent::getFieldControllerData($layoutDef);

        return $value;
    }
}
