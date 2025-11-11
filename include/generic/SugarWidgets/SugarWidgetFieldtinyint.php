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

class SugarWidgetFieldTinyint extends SugarWidgetFieldInt
{
    /**
     * Create a partial SQL query to do an equal query in a WHERE clause.
     *
     * @param $layout_def
     * @return string
     */
    public function queryFilterEquals(&$layout_def)
    {
        $column = $this->_get_column_select($layout_def);

        if (in_array($layout_def['input_name0'][0], ['yes', '1'])) {
            return " {$column} = 1 \n";
        } else {
            return " {$column} IS NULL OR {$column} = 0 \n";
        }
    }
}
